<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\UserService;

class AdminController
{
    private UserService $userService;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->userService = new UserService();
        $this->request = new Request();
    }

    public function users(): void
    {
        $users = $this->userService->getAll();
        Response::view('admin/users', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $this->getCurrentUser(),
            'users' => $users,
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
        ]);
    }

    public function createUser(): void
    {
        Response::view('admin/users_create', [
            'appUrl' => $_ENV['APP_URL'],
            'user' => $this->getCurrentUser(),
            'error' => Session::getFlash('error'),
        ]);
    }

    public function storeUser(): void
    {
        $name = trim($this->request->post('name', ''));
        $email = trim($this->request->post('email', ''));
        $password = $this->request->post('password', '');
        $role = $this->request->post('role', 'user');

        if (empty($name) || empty($email) || empty($password)) {
            Session::flash('error', 'Name, email and password are required.');
            Response::redirect($_ENV['APP_URL'] . '/admin/users/create');
        }

        $this->userService->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);

        Session::flash('success', 'User created successfully.');
        Response::redirect($_ENV['APP_URL'] . '/admin/users');
    }

    public function deleteUser(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        if ((int)$id === $currentUser['id']) {
            Session::flash('error', 'You cannot delete your own account.');
            Response::redirect($_ENV['APP_URL'] . '/admin/users');
        }

        $this->userService->delete((int)$id);
        Session::flash('success', 'User deleted.');
        Response::redirect($_ENV['APP_URL'] . '/admin/users');
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
