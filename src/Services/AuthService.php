<?php
namespace App\Services;

use App\Core\Session;
use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepo->findByEmail($email);
        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        Session::start();
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);

        return true;
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function getCurrentUser(): ?array
    {
        Session::start();
        if (!Session::has('user_id')) {
            return null;
        }
        return [
            'id' => Session::get('user_id'),
            'name' => Session::get('user_name'),
            'email' => Session::get('user_email'),
            'role' => Session::get('user_role'),
        ];
    }

    public function isLoggedIn(): bool
    {
        Session::start();
        return Session::has('user_id');
    }
}
