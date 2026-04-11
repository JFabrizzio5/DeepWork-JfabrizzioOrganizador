<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(): void
    {
        Session::start();
        if (!Session::has('user_id')) {
            Response::redirect($_ENV['APP_URL'] . '/login');
        }
    }
}
