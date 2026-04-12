<?php
namespace App\Services;

use App\Repositories\ProjectRepository;

class ProjectService
{
    private ProjectRepository $repo;

    public function __construct()
    {
        $this->repo = new ProjectRepository();
    }

    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): int
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
