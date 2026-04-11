<?php
namespace App\Models;

class User
{
    public function __construct(
        public readonly int $id = 0,
        public readonly string $name = '',
        public readonly string $email = '',
        public readonly string $password = '',
        public readonly string $role = 'user',
        public readonly string $created_at = '',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            role: $data['role'] ?? 'user',
            created_at: $data['created_at'] ?? '',
        );
    }
}
