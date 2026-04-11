<?php
namespace App\Models;

class Ticket
{
    public function __construct(
        public readonly int $id = 0,
        public readonly string $title = '',
        public readonly string $description = '',
        public readonly string $type = 'support',
        public readonly string $impact = 'medium',
        public readonly string $priority_user = 'medium',
        public readonly string $status = 'new',
        public readonly string $phase = 'information',
        public readonly string $steps_to_reproduce = '',
        public readonly string $technical_context = '',
        public readonly string $requester_name = '',
        public readonly string $requester_email = '',
        public readonly ?int $user_id = null,
        public readonly ?int $assigned_to = null,
        public readonly string $created_at = '',
        public readonly string $updated_at = '',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            title: $data['title'] ?? '',
            description: $data['description'] ?? '',
            type: $data['type'] ?? 'support',
            impact: $data['impact'] ?? 'medium',
            priority_user: $data['priority_user'] ?? 'medium',
            status: $data['status'] ?? 'new',
            phase: $data['phase'] ?? 'information',
            steps_to_reproduce: $data['steps_to_reproduce'] ?? '',
            technical_context: $data['technical_context'] ?? '',
            requester_name: $data['requester_name'] ?? '',
            requester_email: $data['requester_email'] ?? '',
            user_id: isset($data['user_id']) ? (int)$data['user_id'] : null,
            assigned_to: isset($data['assigned_to']) ? (int)$data['assigned_to'] : null,
            created_at: $data['created_at'] ?? '',
            updated_at: $data['updated_at'] ?? '',
        );
    }
}
