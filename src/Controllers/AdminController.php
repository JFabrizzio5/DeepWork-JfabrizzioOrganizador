<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\UserService;
use App\Services\WeeklyPlanService;
use App\Repositories\ApiKeyRepository;

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

    // ──────────────────────────────────────────────
    // API Key management (admin only)
    // ──────────────────────────────────────────────

    public function apiKeys(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $repo    = new ApiKeyRepository();
        $apiKeys = $repo->findAll();
        $users   = $this->userService->getAll();

        Response::view('admin/api_keys', [
            'appUrl'   => $_ENV['APP_URL'],
            'user'     => $currentUser,
            'apiKeys'  => $apiKeys,
            'users'    => $users,
            'newToken' => Session::getFlash('new_token'),
            'success'  => Session::getFlash('success'),
            'error'    => Session::getFlash('error'),
        ]);
    }

    public function generateKey(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $name   = trim($this->request->post('name', ''));
        $userId = (int)$this->request->post('user_id', $currentUser['id']);

        if (empty($name)) {
            Session::flash('error', 'Key label is required.');
            Response::redirect($_ENV['APP_URL'] . '/admin/api-keys');
        }

        // Generate a cryptographically secure 48-byte token (96 hex chars)
        $token = bin2hex(random_bytes(48));

        $repo = new ApiKeyRepository();
        $repo->create($userId, $name, $token);

        // Show the raw token once in the UI
        Session::flash('new_token', $token);
        Session::flash('success', 'API key created successfully.');
        Response::redirect($_ENV['APP_URL'] . '/admin/api-keys');
    }

    public function revokeKey(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $repo = new ApiKeyRepository();
        $repo->revoke((int)$id);

        Session::flash('success', 'API key revoked.');
        Response::redirect($_ENV['APP_URL'] . '/admin/api-keys');
    }

    public function deleteApiKey(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $repo = new ApiKeyRepository();
        $repo->delete((int)$id);

        Session::flash('success', 'API key deleted.');
        Response::redirect($_ENV['APP_URL'] . '/admin/api-keys');
    }

    public function weeklyDashboard(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $planService  = new WeeklyPlanService();
        $summaries    = $planService->getWeekSummaries(12);
        $recentPlans  = $planService->findRecentPlans(8);

        // Group recent plans by week_start
        $byWeek = [];
        foreach ($recentPlans as $plan) {
            $byWeek[$plan['week_start']][] = $plan;
        }
        krsort($byWeek);

        Response::view('admin/weekly_dashboard', [
            'appUrl'      => $_ENV['APP_URL'],
            'user'        => $currentUser,
            'summaries'   => $summaries,
            'byWeek'      => $byWeek,
            'success'     => Session::getFlash('success'),
            'error'       => Session::getFlash('error'),
        ]);
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
