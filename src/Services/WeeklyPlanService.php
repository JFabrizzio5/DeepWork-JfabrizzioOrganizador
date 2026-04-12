<?php
namespace App\Services;

use App\Core\Database;
use App\Repositories\WeeklyPlanRepository;

class WeeklyPlanService
{
    private WeeklyPlanRepository $planRepo;

    public function __construct()
    {
        $this->planRepo = new WeeklyPlanRepository();
    }

    public function getAll(array $filters = []): array
    {
        return $this->planRepo->findAll($filters);
    }

    public function getById(int $id): ?array
    {
        $plan = $this->planRepo->findById($id);
        if ($plan) {
            $plan['tasks'] = $this->planRepo->getTasksByPlanId($id);
        }
        return $plan;
    }

    public function create(array $data, int $userId): int
    {
        $data['created_by'] = $userId;
        $planId = $this->planRepo->create($data);

        if (!empty($data['tasks']) && is_array($data['tasks'])) {
            foreach ($data['tasks'] as $taskTitle) {
                $taskTitle = trim($taskTitle);
                if ($taskTitle !== '') {
                    $this->planRepo->addTask($planId, $taskTitle);
                }
            }
        }

        return $planId;
    }

    public function update(int $id, array $data): bool
    {
        return $this->planRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->planRepo->delete($id);
    }

    public function addTask(int $planId, string $title): bool
    {
        return $this->planRepo->addTask($planId, $title) > 0;
    }

    public function toggleTask(int $taskId): bool
    {
        $task = $this->planRepo->findTaskById($taskId);
        if (!$task) {
            return false;
        }
        $newStatus = $task['status'] === 'done' ? 'not_done' : 'done';
        $result = $this->planRepo->updateTask($taskId, $newStatus);
        if ($result) {
            $this->recalculateProgress((int)$task['plan_id']);
        }
        return $result;
    }

    public function recalculateProgress(int $planId): void
    {
        $tasks = $this->planRepo->getTasksByPlanId($planId);
        if (empty($tasks)) {
            $this->planRepo->update($planId, ['progress_percent' => 0]);
            return;
        }
        $done = count(array_filter($tasks, fn($t) => $t['status'] === 'done'));
        $percent = (int)round(($done / count($tasks)) * 100);
        $this->planRepo->update($planId, ['progress_percent' => $percent]);
    }

    public function copyToNextWeek(int $planId, int $userId): int
    {
        $plan = $this->getById($planId);
        if (!$plan) {
            return 0;
        }
        $nextWeekStart = date('Y-m-d', strtotime($plan['week_start'] . ' +7 days'));

        $newPlanId = $this->planRepo->create([
            'week_start'      => $nextWeekStart,
            'project'         => $plan['project'],
            'summary'         => $plan['summary'],
            'assigned_to'     => $plan['assigned_to'] ?? null,
            'status'          => 'pending',
            'progress_percent'=> 0,
            'file_path'       => null,
            'created_by'      => $userId,
        ]);

        foreach ($plan['tasks'] as $task) {
            $this->planRepo->addTask($newPlanId, $task['title']);
        }

        return $newPlanId;
    }

    public function getWeekSummaries(int $limit = 10): array
    {
        return $this->planRepo->getWeekSummaries($limit);
    }

    public function findRecentPlans(int $weeks = 4): array
    {
        $plans = $this->planRepo->findRecentPlans($weeks);
        foreach ($plans as &$plan) {
            $plan['tasks'] = $this->planRepo->getTasksByPlanId((int)$plan['id']);
        }
        unset($plan);
        return $plans;
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->planRepo->update($id, ['status' => $status]);
    }
}
