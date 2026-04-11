<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Static holder for the authenticated API user.
 * Populated by ApiKeyMiddleware before the controller action runs.
 */
class ApiAuth
{
    private static ?array $user = null;

    public static function set(array $user): void
    {
        self::$user = $user;
    }

    public static function user(): ?array
    {
        return self::$user;
    }

    public static function id(): ?int
    {
        return self::$user ? (int) self::$user['id'] : null;
    }

    public static function role(): ?string
    {
        return self::$user['role'] ?? null;
    }

    /**
     * Returns true when the authenticated user has one of the given roles.
     */
    public static function hasRole(string ...$roles): bool
    {
        return in_array(self::$user['role'] ?? '', $roles, true);
    }
}
