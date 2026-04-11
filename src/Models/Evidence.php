<?php
namespace App\Models;

class Evidence
{
    public function __construct(
        public readonly int $id = 0,
        public readonly int $ticket_id = 0,
        public readonly int $user_id = 0,
        public readonly string $filename = '',
        public readonly string $original_name = '',
        public readonly string $file_type = '',
        public readonly int $file_size = 0,
        public readonly string $created_at = '',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            ticket_id: (int)($data['ticket_id'] ?? 0),
            user_id: (int)($data['user_id'] ?? 0),
            filename: $data['filename'] ?? '',
            original_name: $data['original_name'] ?? '',
            file_type: $data['file_type'] ?? '',
            file_size: (int)($data['file_size'] ?? 0),
            created_at: $data['created_at'] ?? '',
        );
    }
}
