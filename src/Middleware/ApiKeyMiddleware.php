<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\ApiAuth;
use App\Core\Response;
use App\Repositories\ApiKeyRepository;

/**
 * Validates the API key from:
 *   • Authorization: Bearer <token>   header  (preferred)
 *   • ?api_key=<token>                query param (fallback)
 *
 * On success, sets the authenticated user in ApiAuth.
 * On failure, returns a 401 JSON response.
 */
class ApiKeyMiddleware
{
    public function handle(): void
    {
        $token = $this->extractToken();

        if ($token === null) {
            Response::json([
                'error' => 'API key required. Use "Authorization: Bearer <token>" header or "?api_key=<token>" query param.',
            ], 401);
        }

        $repo = new ApiKeyRepository();
        $keyData = $repo->findByToken($token);

        if ($keyData === null) {
            Response::json(['error' => 'Invalid or revoked API key.'], 401);
        }

        // Record last usage (fire-and-forget; no need to check return value)
        $repo->touchLastUsed((int) $keyData['id']);

        // Populate static context so controllers can call ApiAuth::user() etc.
        ApiAuth::set([
            'id'    => (int) $keyData['user_id'],
            'name'  => $keyData['user_name'],
            'email' => $keyData['user_email'],
            'role'  => $keyData['user_role'],
        ]);
    }

    private function extractToken(): ?string
    {
        // 1. Authorization header
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($header, 'Bearer ')) {
            $t = trim(substr($header, 7));
            if ($t !== '') {
                return $t;
            }
        }

        // 2. Query string fallback
        $t = trim($_GET['api_key'] ?? '');
        return $t !== '' ? $t : null;
    }
}
