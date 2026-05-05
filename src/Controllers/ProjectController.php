<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\ProjectService;

class ProjectController
{
    private ProjectService $projectService;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->projectService = new ProjectService();
        $this->request = new Request();
    }

    // ── Admin CRUD ────────────────────────────────────────

    public function index(): void
    {
        $this->requireAdmin();
        $projects = $this->projectService->getAll();
        Response::view('admin/projects', [
            'appUrl'   => $_ENV['APP_URL'],
            'user'     => $this->getCurrentUser(),
            'projects' => $projects,
            'success'  => Session::getFlash('success'),
            'error'    => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        Response::view('admin/projects_create', [
            'appUrl' => $_ENV['APP_URL'],
            'user'   => $this->getCurrentUser(),
            'editProject' => null,
            'error'  => Session::getFlash('error'),
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $name  = trim($this->request->post('name', ''));
        $color = trim($this->request->post('color', '#3B82F6'));
        $desc  = trim($this->request->post('description', ''));

        if (empty($name)) {
            Session::flash('error', 'Project name is required.');
            Response::redirect($_ENV['APP_URL'] . '/admin/projects/create');
        }

        $this->projectService->create([
            'name'        => $name,
            'color'       => $color,
            'description' => $desc,
        ]);

        Session::flash('success', 'Project created successfully.');
        Response::redirect($_ENV['APP_URL'] . '/admin/projects');
    }

    public function edit(string $id): void
    {
        $this->requireAdmin();
        $project = $this->projectService->getById((int)$id);
        if (!$project) {
            Response::abort(404, 'Project not found.');
        }
        Response::view('admin/projects_create', [
            'appUrl'      => $_ENV['APP_URL'],
            'user'        => $this->getCurrentUser(),
            'editProject' => $project,
            'error'       => Session::getFlash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAdmin();
        $name  = trim($this->request->post('name', ''));
        $color = trim($this->request->post('color', '#3B82F6'));
        $desc  = trim($this->request->post('description', ''));

        if (empty($name)) {
            Session::flash('error', 'Project name is required.');
            Response::redirect($_ENV['APP_URL'] . '/admin/projects/' . $id . '/edit');
        }

        $this->projectService->update((int)$id, [
            'name'        => $name,
            'color'       => $color,
            'description' => $desc,
        ]);

        Session::flash('success', 'Project updated.');
        Response::redirect($_ENV['APP_URL'] . '/admin/projects');
    }

    public function delete(string $id): void
    {
        $this->requireAdmin();
        $this->projectService->delete((int)$id);
        Session::flash('success', 'Project deleted.');
        Response::redirect($_ENV['APP_URL'] . '/admin/projects');
    }

    // ── Project detail (all assigned users + admin) ───────

    public function show(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        $project = $this->projectService->getById((int)$id);
        if (!$project) {
            Response::abort(404, 'Proyecto no encontrado.');
        }

        // Only admin or users assigned to this project may view it
        if ($currentUser['role'] !== 'admin' && !$this->projectService->userHasProject($currentUser['id'], (int)$id)) {
            Response::abort(403, 'Acceso denegado.');
        }

        $files   = $this->projectService->getFiles((int)$id);
        $notes   = $this->projectService->getNotes((int)$id);
        $profile = $this->projectService->getProfile($currentUser['id'], (int)$id);
        $members = $this->projectService->getUsersForProject((int)$id);

        Response::view('projects/show', [
            'appUrl'  => $_ENV['APP_URL'],
            'user'    => $currentUser,
            'project' => $project,
            'files'   => $files,
            'notes'   => $notes,
            'profile' => $profile,
            'members' => $members,
            'success' => Session::getFlash('success'),
            'error'   => Session::getFlash('error'),
        ]);
    }

    // ── File upload / download / delete ───────────────────

    public function uploadFile(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        $project = $this->projectService->getById((int)$id);
        if (!$project) {
            Response::abort(404, 'Proyecto no encontrado.');
        }

        // Only admin and dev (programadores) can upload resources
        if (!in_array($currentUser['role'], ['admin', 'dev'], true)) {
            Response::abort(403, 'Solo programadores y administradores pueden subir recursos.');
        }

        // Admin sees all projects; dev must be assigned to this project
        if ($currentUser['role'] === 'dev' && !$this->projectService->userHasProject($currentUser['id'], (int)$id)) {
            Response::abort(403, 'No tienes acceso a este proyecto.');
        }

        $file = $_FILES['project_file'] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'Selecciona un archivo para subir.');
            Response::redirect($_ENV['APP_URL'] . '/projects/' . $id);
        }

        $description = trim($this->request->post('description', ''));
        $result = $this->projectService->uploadFile($file, (int)$id, $currentUser['id'], $description);

        if ($result === true) {
            Session::flash('success', 'Archivo subido exitosamente.');
        } else {
            Session::flash('error', $result);
        }
        Response::redirect($_ENV['APP_URL'] . '/projects/' . $id);
    }

    public function serveFile(string $id, string $fileId): void
    {
        $currentUser = $this->getCurrentUser();
        $file = $this->projectService->getFileById((int)$fileId);

        if (!$file || (int)$file['project_id'] !== (int)$id) {
            Response::abort(404, 'Archivo no encontrado.');
        }

        if ($currentUser['role'] !== 'admin' && !$this->projectService->userHasProject($currentUser['id'], (int)$id)) {
            Response::abort(403, 'Acceso denegado.');
        }

        $filePath = dirname(__DIR__, 2) . '/storage/projects/' . $id . '/' . $file['filename'];
        if (!file_exists($filePath)) {
            Response::abort(404, 'Archivo no encontrado en disco.');
        }

        $safeName = preg_replace('/[^a-zA-Z0-9._\-]/', '_', basename($file['original_name']));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $safeName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('X-Content-Type-Options: nosniff');
        readfile($filePath);
        exit;
    }

    public function deleteFile(string $id, string $fileId): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::abort(403, 'Solo administradores pueden eliminar archivos.');
        }

        $deleted = $this->projectService->deleteFile((int)$id, (int)$fileId);
        if ($deleted) {
            Session::flash('success', 'Archivo eliminado.');
        } else {
            Session::flash('error', 'No se pudo eliminar el archivo.');
        }
        Response::redirect($_ENV['APP_URL'] . '/projects/' . $id);
    }

    // ── Notes ─────────────────────────────────────────────

    public function addNote(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        $project = $this->projectService->getById((int)$id);
        if (!$project) {
            Response::abort(404, 'Proyecto no encontrado.');
        }

        if ($currentUser['role'] !== 'admin' && !$this->projectService->userHasProject($currentUser['id'], (int)$id)) {
            Response::abort(403, 'Acceso denegado.');
        }

        $note = trim($this->request->post('note', ''));
        if (empty($note)) {
            Session::flash('error', 'El mensaje no puede estar vacío.');
            Response::redirect($_ENV['APP_URL'] . '/projects/' . $id);
        }

        $this->projectService->addNote((int)$id, $currentUser['id'], $note);
        Session::flash('success', 'Mensaje agregado.');
        Response::redirect($_ENV['APP_URL'] . '/projects/' . $id);
    }

    public function deleteNote(string $id, string $noteId): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::abort(403, 'Solo administradores pueden eliminar mensajes.');
        }

        $this->projectService->deleteNote((int)$id, (int)$noteId);
        Session::flash('success', 'Mensaje eliminado.');
        Response::redirect($_ENV['APP_URL'] . '/projects/' . $id);
    }

    // ── Per-project profile ───────────────────────────────

    public function updateProfile(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        $project = $this->projectService->getById((int)$id);
        if (!$project) {
            Response::abort(404, 'Proyecto no encontrado.');
        }

        // Admin can update any user's profile; others can only update their own
        $targetUserId = $currentUser['id'];
        if ($currentUser['role'] === 'admin') {
            $postedId = (int)$this->request->post('user_id', 0);
            if ($postedId > 0) {
                $targetUserId = $postedId;
            }
        }

        if ($currentUser['role'] !== 'admin' && !$this->projectService->userHasProject($currentUser['id'], (int)$id)) {
            Response::abort(403, 'Acceso denegado.');
        }

        $this->projectService->updateProfile($targetUserId, (int)$id, [
            'display_name' => trim($this->request->post('display_name', '')),
            'bio'          => trim($this->request->post('bio', '')),
            'contact_info' => trim($this->request->post('contact_info', '')),
        ]);

        Session::flash('success', 'Perfil de proyecto actualizado.');
        Response::redirect($_ENV['APP_URL'] . '/projects/' . $id);
    }

    // ── Admin: project members overview ───────────────────

    public function members(string $id): void
    {
        $this->requireAdmin();
        $project = $this->projectService->getById((int)$id);
        if (!$project) {
            Response::abort(404, 'Project not found.');
        }

        $members  = $this->projectService->getUsersForProject((int)$id);
        $profiles = $this->projectService->getProfilesByProject((int)$id);

        // Key profiles by user_id for easy lookup
        $profileMap = [];
        foreach ($profiles as $p) {
            $profileMap[(int)$p['user_id']] = $p;
        }

        Response::view('admin/project_members', [
            'appUrl'     => $_ENV['APP_URL'],
            'user'       => $this->getCurrentUser(),
            'project'    => $project,
            'members'    => $members,
            'profileMap' => $profileMap,
            'success'    => Session::getFlash('success'),
            'error'      => Session::getFlash('error'),
        ]);
    }

    // ── User: list my projects ────────────────────────────

    public function myProjects(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] === 'admin') {
            $projects = $this->projectService->getAll();
        } else {
            $projects = $this->projectService->getProjectsForUser($currentUser['id']);
        }

        Response::view('projects/index', [
            'appUrl'   => $_ENV['APP_URL'],
            'user'     => $currentUser,
            'projects' => $projects,
            'success'  => Session::getFlash('success'),
            'error'    => Session::getFlash('error'),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────

    private function requireAdmin(): void
    {
        $user = $this->getCurrentUser();
        if ($user['role'] !== 'admin') {
            Response::abort(403, 'Access denied.');
        }
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
