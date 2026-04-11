<?php
namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function getAll(): array
    {
        return $this->userRepo->findAll();
    }

    public function getById(int $id): ?array
    {
        return $this->userRepo->findById($id);
    }

    public function create(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->userRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        return $this->userRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->userRepo->delete($id);
    }

    public function getDevelopers(): array
    {
        return $this->userRepo->findByRole('dev');
    }
}
