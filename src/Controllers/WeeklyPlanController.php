<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\WeeklyPlanService;
use App\Services\UserService;
use App\Services\ProjectService;

class WeeklyPlanController
{
    private WeeklyPlanService $planService;
    private UserService $userService;
    private ProjectService $projectService;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->planService    = new WeeklyPlanService();
        $this->userService    = new UserService();
        $this->projectService = new ProjectService();
        $this->request        = new Request();
    }

    public function index(): void
    {
        $filters = [
            'project'     => $this->request->get('project', ''),
            'status'      => $this->request->get('status', ''),
            'assigned_to' => $this->request->get('assigned_to', ''),
            'week_start'  => $this->request->get('week_start', ''),
        ];
        $plans      = $this->planService->getAll($filters);
        $developers = $this->userService->getDevelopers();
        $projects   = $this->projectService->getAll();

        Response::view('weekly/index', [
            'appUrl'     => $_ENV['APP_URL'],
            'user'       => $this->getCurrentUser(),
            'plans'      => $plans,
            'filters'    => $filters,
            'developers' => $developers,
            'projects'   => $projects,
            'success'    => Session::getFlash('success'),
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $developers = $this->userService->getDevelopers();
        $allUsers   = $this->userService->getAll();
        $projects   = $this->projectService->getAll();

        Response::view('weekly/create', [
            'appUrl'     => $_ENV['APP_URL'],
            'user'       => $user,
            'developers' => $developers,
            'allUsers'   => $allUsers,
            'projects'   => $projects,
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function store(): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $weekStart = $this->request->post('week_start', '');
        if (empty($weekStart)) {
            Session::flash('error', 'Week start date is required.');
            Response::redirect($_ENV['APP_URL'] . '/weekly-plan/create');
        }

        $filePath = null;
        if (!empty($_FILES['plan_file']['name'])) {
            $file    = $_FILES['plan_file'];
            $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'xlsx', 'xls', 'doc', 'docx'];
            if (in_array($ext, $allowed) && $file['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/weekly/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = uniqid('wp_', true) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
                $filePath = 'weekly/' . $filename;
            }
        }

        $tasks = $this->request->post('tasks', []);
        if (!is_array($tasks)) {
            $tasks = [];
        }

        $planId = $this->planService->create([
            'week_start'  => $weekStart,
            'project'     => $this->request->post('project', ''),
            'summary'     => $this->request->post('summary', ''),
            'assigned_to' => $this->request->post('assigned_to', null) ?: null,
            'status'      => 'pending',
            'file_path'   => $filePath,
            'tasks'       => $tasks,
        ], $user['id']);

        Session::flash('success', 'Weekly plan created.');
        Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $planId);
    }

    public function show(string $id): void
    {
        $plan     = $this->planService->getById((int)$id);
        $projects = $this->projectService->getAll();
        $allUsers = $this->userService->getAll();
        if (!$plan) {
            Response::abort(404, 'Plan not found.');
        }

        Response::view('weekly/show', [
            'appUrl'   => $_ENV['APP_URL'],
            'user'     => $this->getCurrentUser(),
            'plan'     => $plan,
            'projects' => $projects,
            'allUsers' => $allUsers,
            'success'  => Session::getFlash('success'),
            'error'    => Session::getFlash('error'),
        ]);
    }

    public function addTask(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $title = trim($this->request->post('title', ''));
        if (empty($title)) {
            Session::flash('error', 'Task title is required.');
            Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $id);
        }

        $assignedTo = $this->request->post('assigned_to', null) ?: null;
        $this->planService->addTask((int)$id, $title, $assignedTo ? (int)$assignedTo : null);
        $this->planService->recalculateProgress((int)$id);
        Session::flash('success', 'Task added.');
        Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $id);
    }

    public function toggleTask(): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $taskId = (int)$this->request->post('task_id', 0);
        $planId = (int)$this->request->post('plan_id', 0);

        $this->planService->toggleTask($taskId);
        Session::flash('success', 'Task updated.');
        Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $planId);
    }

    public function updateTaskStatus(): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $taskId     = (int)$this->request->post('task_id', 0);
        $planId     = (int)$this->request->post('plan_id', 0);
        $status     = $this->request->post('status', '');
        $assignedTo = $this->request->post('assigned_to', null) ?: null;

        $allowed = ['pending', 'in_progress', 'done'];
        if (!in_array($status, $allowed)) {
            Session::flash('error', 'Estado no válido.');
            Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $planId);
        }

        $this->planService->updateTaskStatus($taskId, $status, $assignedTo ? (int)$assignedTo : null);
        Session::flash('success', 'Tarea actualizada.');
        Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $planId);
    }

    public function updateStatus(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $status   = $this->request->post('status', '');
        $allowed  = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $allowed)) {
            Session::flash('error', 'Invalid status.');
            Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $id);
        }

        $this->planService->updateStatus((int)$id, $status);
        Session::flash('success', 'Plan status updated.');
        Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $id);
    }

    public function copyToNextWeek(string $id): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $newId = $this->planService->copyToNextWeek((int)$id, $user['id']);
        if ($newId) {
            Session::flash('success', 'Plan copied to next week.');
            Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $newId);
        } else {
            Session::flash('error', 'Could not copy plan.');
            Response::redirect($_ENV['APP_URL'] . '/weekly-plan/' . $id);
        }
    }

    public function importExcel(): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        // Receives JSON payload from the client-side SheetJS parse
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data || empty($data['week_start'])) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid data. week_start is required.']);
            exit;
        }

        $tasks = [];
        if (!empty($data['tasks']) && is_array($data['tasks'])) {
            foreach ($data['tasks'] as $t) {
                $title = trim((string)($t['title'] ?? $t['Task'] ?? $t['task'] ?? ''));
                if ($title !== '') {
                    $tasks[] = $title;
                }
            }
        }

        $planId = $this->planService->create([
            'week_start'  => $data['week_start'],
            'project'     => $data['project'] ?? '',
            'summary'     => $data['summary'] ?? '',
            'assigned_to' => !empty($data['assigned_to']) ? (int)$data['assigned_to'] : null,
            'status'      => 'pending',
            'file_path'   => null,
            'tasks'       => $tasks,
        ], $user['id']);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $planId, 'redirect' => $_ENV['APP_URL'] . '/weekly-plan/' . $planId]);
        exit;
    }

    public function delete(string $id): void
    {
        $user = $this->getCurrentUser();
        if ($user['role'] !== 'admin') {
            Response::abort(403, 'Access denied.');
        }

        $this->planService->delete((int)$id);
        Session::flash('success', 'Plan deleted.');
        Response::redirect($_ENV['APP_URL'] . '/weekly-plan');
    }

    private function getCurrentUser(): array
    {
        return [
            'id'    => (int)Session::get('user_id'),
            'name'  => Session::get('user_name'),
            'email' => Session::get('user_email'),
            'role'  => Session::get('user_role'),
        ];
    }
}
