<?php
namespace App\Models;

class WeeklyPlan
{
    public function __construct(
        public readonly int $id = 0,
        public readonly string $week_start = '',
        public readonly string $project = 'A',
        public readonly string $summary = '',
        public readonly ?int $assigned_to = null,
        public readonly string $status = 'pending',
        public readonly int $progress_percent = 0,
        public readonly string $file_path = '',
        public readonly ?int $created_by = null,
        public readonly string $created_at = '',
        public readonly string $updated_at = '',
        public readonly array $tasks = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            week_start: $data['week_start'] ?? '',
            project: $data['project'] ?? 'A',
            summary: $data['summary'] ?? '',
            assigned_to: isset($data['assigned_to']) ? (int)$data['assigned_to'] : null,
            status: $data['status'] ?? 'pending',
            progress_percent: (int)($data['progress_percent'] ?? 0),
            file_path: $data['file_path'] ?? '',
            created_by: isset($data['created_by']) ? (int)$data['created_by'] : null,
            created_at: $data['created_at'] ?? '',
            updated_at: $data['updated_at'] ?? '',
            tasks: $data['tasks'] ?? [],
        );
    }
}
