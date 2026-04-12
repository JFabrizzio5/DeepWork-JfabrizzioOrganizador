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
