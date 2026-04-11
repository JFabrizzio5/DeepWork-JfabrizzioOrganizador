<?php
namespace App\Services;

use App\Repositories\KnowledgeBaseRepository;

class KnowledgeBaseService
{
    private KnowledgeBaseRepository $kbRepo;

    public function __construct()
    {
        $this->kbRepo = new KnowledgeBaseRepository();
    }

    public function getAll(array $filters = []): array
    {
        return $this->kbRepo->findAll($filters);
    }

    public function getById(int $id): ?array
    {
        return $this->kbRepo->findById($id);
    }

    public function search(string $query): array
    {
        return $this->kbRepo->search($query);
    }

    public function create(array $data, int $userId): int
    {
        $data['created_by'] = $userId;
        return $this->kbRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->kbRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->kbRepo->delete($id);
    }
}
