<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class RoleMiddleware
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(): void
    {
        Session::start();
        $userRole = Session::get('user_role', '');
        if (!in_array($userRole, $this->allowedRoles)) {
            Response::abort(403, 'Access Denied: You do not have permission to access this resource.');
        }
    }

    public static function for(array $roles): callable
    {
        return function () use ($roles) {
            $mw = new self($roles);
            $mw->handle();
        };
    }
}
