<?php
namespace App\Models;

class KnowledgeBase
{
    public function __construct(
        public readonly int $id = 0,
        public readonly string $title = '',
        public readonly string $content = '',
        public readonly string $tags = '',
        public readonly string $links = '',
        public readonly string $tag_type = 'documentation',
        public readonly ?int $created_by = null,
        public readonly string $created_at = '',
        public readonly string $updated_at = '',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            title: $data['title'] ?? '',
            content: $data['content'] ?? '',
            tags: $data['tags'] ?? '',
            links: $data['links'] ?? '',
            tag_type: $data['tag_type'] ?? 'documentation',
            created_by: isset($data['created_by']) ? (int)$data['created_by'] : null,
            created_at: $data['created_at'] ?? '',
            updated_at: $data['updated_at'] ?? '',
        );
    }
}
