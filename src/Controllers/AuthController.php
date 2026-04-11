<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;
    private Request $request;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->request = new Request();
    }

    public function showLogin(): void
    {
        Session::start();
        if ($this->authService->isLoggedIn()) {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }
        Response::view('auth/login', [
            'appUrl' => $_ENV['APP_URL'],
            'error' => Session::getFlash('error'),
        ]);
    }

    public function processLogin(): void
    {
        Session::start();
        $email = trim($this->request->post('email', ''));
        $password = $this->request->post('password', '');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email and password are required.');
            Response::redirect($_ENV['APP_URL'] . '/login');
        }

        if ($this->authService->login($email, $password)) {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        } else {
            Session::flash('error', 'Invalid email or password.');
            Response::redirect($_ENV['APP_URL'] . '/login');
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
        Response::redirect($_ENV['APP_URL'] . '/login');
    }
}
