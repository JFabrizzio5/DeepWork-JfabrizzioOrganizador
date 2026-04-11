<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\WeeklyPlanService;
use App\Services\UserService;

class WeeklyPlanController
{
    private WeeklyPlanService $planService;
    private UserService $userService;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->planService = new WeeklyPlanService();
        $this->userService = new UserService();
        $this->request = new Request();
    }

    public function index(): void
    {
        $filters = [
            'project'     => $this->request->get('project', ''),
            'status'      => $this->request->get('status', ''),
            'assigned_to' => $this->request->get('assigned_to', ''),
        ];
        $plans = $this->planService->getAll($filters);
        $developers = $this->userService->getDevelopers();

        Response::view('weekly/index', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $this->getCurrentUser(),
            'plans' => $plans,
            'filters' => $filters,
            'developers' => $developers,
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'dev'])) {
            Response::abort(403, 'Access denied.');
        }

        $developers = $this->userService->getDevelopers();
        $allUsers = $this->userService->getAll();

        Response::view('weekly/create', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $user,
            'developers' => $developers,
            'allUsers' => $allUsers,
            'error' => Session::getFlash('error'),
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
            $file = $_FILES['plan_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
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
            'project'     => $this->request->post('project', 'A'),
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
        $plan = $this->planService->getById((int)$id);
        if (!$plan) {
            Response::abort(404, 'Plan not found.');
        }

        Response::view('weekly/show', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $this->getCurrentUser(),
            'plan' => $plan,
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
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

        $this->planService->addTask((int)$id, $title);
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
