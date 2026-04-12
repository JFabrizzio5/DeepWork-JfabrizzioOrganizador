<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\UserService;
use App\Services\WeeklyPlanService;
use App\Repositories\ApiKeyRepository;
use App\Repositories\SucursalRepository;

class AdminController
{
    private UserService $userService;
    private SucursalRepository $sucursalRepo;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->userService  = new UserService();
        $this->sucursalRepo = new SucursalRepository();
        $this->request      = new Request();
    }

    public function users(): void
    {
        $users      = $this->userService->getAll();
        $sucursales = $this->sucursalRepo->findAll();
        Response::view('admin/users', [
            'appUrl'    => $_ENV['APP_URL'],
            'user'      => $this->getCurrentUser(),
            'users'     => $users,
            'sucursales' => $sucursales,
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function createUser(): void
    {
        Response::view('admin/users_create', [
            'appUrl' => $_ENV['APP_URL'],
            'user'   => $this->getCurrentUser(),
            'error'  => Session::getFlash('error'),
        ]);
    }

    public function storeUser(): void
    {
        $name     = trim($this->request->post('name', ''));
        $email    = trim($this->request->post('email', ''));
        $password = $this->request->post('password', '');
        $role     = $this->request->post('role', 'user');

        if (empty($name) || empty($email) || empty($password)) {
            Session::flash('error', 'Nombre, correo y contraseña son requeridos.');
            Response::redirect($_ENV['APP_URL'] . '/admin/users/create');
        }

        $this->userService->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => $role,
        ]);

        Session::flash('success', 'Usuario creado exitosamente.');
        Response::redirect($_ENV['APP_URL'] . '/admin/users');
    }

    public function deleteUser(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        if ((int)$id === $currentUser['id']) {
            Session::flash('error', 'No puedes eliminar tu propia cuenta.');
            Response::redirect($_ENV['APP_URL'] . '/admin/users');
        }

        $this->userService->delete((int)$id);
        Session::flash('success', 'Usuario eliminado.');
        Response::redirect($_ENV['APP_URL'] . '/admin/users');
    }

    public function updateUserHighlight(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/admin/users');
        }

        $isVip = (int)$this->request->post('is_vip', 0);
        $color = trim($this->request->post('highlight_color', '#F59E0B'));

        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $color = '#F59E0B';
        }

        $this->userService->setHighlight((int)$id, $isVip, $color);
        Session::flash('success', 'Destacado actualizado.');
        Response::redirect($_ENV['APP_URL'] . '/admin/users');
    }

    public function updateUserSucursales(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/admin/users');
        }

        $sucursalIds = $this->request->post('sucursal_ids', []);
        if (!is_array($sucursalIds)) {
            $sucursalIds = [];
        }

        $this->sucursalRepo->setUserSucursales((int)$id, $sucursalIds);
        Session::flash('success', 'Sucursales del usuario actualizadas.');
        Response::redirect($_ENV['APP_URL'] . '/admin/users');
    }

    // ──────────────────────────────────────────────
    // Sucursal management
    // ──────────────────────────────────────────────

    public function sucursales(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $sucursales = $this->sucursalRepo->findAll();
        Response::view('admin/sucursales', [
            'appUrl'    => $_ENV['APP_URL'],
            'user'      => $currentUser,
            'sucursales' => $sucursales,
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function storeSucursal(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $nombre      = trim($this->request->post('nombre', ''));
        $descripcion = trim($this->request->post('descripcion', '')) ?: null;

        if (empty($nombre)) {
            Session::flash('error', 'El nombre de la sucursal es requerido.');
            Response::redirect($_ENV['APP_URL'] . '/admin/sucursales');
        }

        $this->sucursalRepo->create($nombre, $descripcion);
        Session::flash('success', 'Sucursal creada exitosamente.');
        Response::redirect($_ENV['APP_URL'] . '/admin/sucursales');
    }

    public function deleteSucursal(string $id): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $this->sucursalRepo->delete((int)$id);
        Session::flash('success', 'Sucursal eliminada.');
        Response::redirect($_ENV['APP_URL'] . '/admin/sucursales');
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
            Session::flash('error', 'La etiqueta de la clave es requerida.');
            Response::redirect($_ENV['APP_URL'] . '/admin/api-keys');
        }

        $token = bin2hex(random_bytes(48));

        $repo = new ApiKeyRepository();
        $repo->create($userId, $name, $token);

        Session::flash('new_token', $token);
        Session::flash('success', 'Clave API creada exitosamente.');
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

        Session::flash('success', 'Clave API revocada.');
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

        Session::flash('success', 'Clave API eliminada.');
        Response::redirect($_ENV['APP_URL'] . '/admin/api-keys');
    }

    public function weeklyDashboard(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['role'] !== 'admin') {
            Response::redirect($_ENV['APP_URL'] . '/tickets/list');
        }

        $planService = new WeeklyPlanService();
        $summaries   = $planService->getWeekSummaries(12);
        $recentPlans = $planService->findRecentPlans(8);

        $byWeek = [];
        foreach ($recentPlans as $plan) {
            $byWeek[$plan['week_start']][] = $plan;
        }
        krsort($byWeek);

        Response::view('admin/weekly_dashboard', [
            'appUrl'    => $_ENV['APP_URL'],
            'user'      => $currentUser,
            'summaries' => $summaries,
            'byWeek'    => $byWeek,
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
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
