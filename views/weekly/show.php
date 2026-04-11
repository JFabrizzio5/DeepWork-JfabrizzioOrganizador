<?php
$pageTitle = 'Weekly Plan — Week of ' . date('M j, Y', strtotime($plan['week_start'] ?? 'now'));
$statusColors = [
    'pending'     => 'bg-slate-700 text-slate-300 border-slate-600',
    'in_progress' => 'bg-yellow-900/50 text-yellow-300 border-yellow-700',
    'completed'   => 'bg-green-900/50 text-green-300 border-green-700',
];
$projectColors = [
    'A' => 'bg-blue-700',
    'B' => 'bg-purple-700',
    'C' => 'bg-green-700',
    'D' => 'bg-orange-700',
];
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="max-w-3xl mx-auto">
                <div class="mb-6 flex items-center justify-between">
                    <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan" class="text-slate-400 hover:text-white text-sm transition-colors">← Back to Plans</a>
                    <?php if ($user['role'] === 'admin'): ?>
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>/delete"
                          onsubmit="return confirm('Delete this weekly plan?')">
                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition-colors">Delete Plan</button>
                    </form>
                    <?php endif; ?>
                </div>

                <!-- Plan Header -->
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 mb-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-sm font-bold text-white px-3 py-1 rounded-lg <?= $projectColors[$plan['project']] ?? 'bg-slate-700' ?>">
                                    Project <?= htmlspecialchars($plan['project']) ?>
                                </span>
                                <span class="text-xs px-3 py-1 rounded border <?= $statusColors[$plan['status']] ?? '' ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', ucfirst($plan['status']))) ?>
                                </span>
                            </div>
                            <h2 class="text-xl font-bold text-white">Week of <?= htmlspecialchars(date('F j, Y', strtotime($plan['week_start']))) ?></h2>
                            <p class="text-slate-400 text-sm mt-1">
                                Assigned to: <strong class="text-slate-300"><?= htmlspecialchars($plan['assigned_name'] ?? 'Unassigned') ?></strong>
                                · Created by: <?= htmlspecialchars($plan['creator_name'] ?? 'Unknown') ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-white"><?= htmlspecialchars((string)$plan['progress_percent']) ?>%</p>
                            <p class="text-xs text-slate-500">complete</p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="w-full bg-slate-700 rounded-full h-3">
                            <div class="bg-blue-500 h-3 rounded-full transition-all" style="width: <?= min(100, (int)$plan['progress_percent']) ?>%"></div>
                        </div>
                    </div>

                    <?php if (!empty($plan['summary'])): ?>
                    <div class="mt-4 bg-slate-900/50 rounded-lg p-4 text-slate-300 text-sm whitespace-pre-wrap">
                        <?= htmlspecialchars($plan['summary']) ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($plan['file_path'])): ?>
                    <div class="mt-4">
                        <a href="<?= htmlspecialchars($appUrl) ?>/uploads/<?= htmlspecialchars($plan['file_path']) ?>"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download attached plan file
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tasks -->
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        Tasks
                        <span class="text-slate-400 text-sm font-normal">
                            (<?= count(array_filter($plan['tasks'] ?? [], fn($t) => $t['status'] === 'done')) ?>/<?= count($plan['tasks'] ?? []) ?> done)
                        </span>
                    </h3>

                    <?php if (empty($plan['tasks'])): ?>
                    <p class="text-slate-500 text-sm">No tasks yet.</p>
                    <?php else: ?>
                    <ul class="space-y-2 mb-4">
                        <?php foreach ($plan['tasks'] as $task): ?>
                        <li class="flex items-center gap-3 bg-slate-900/50 rounded-lg px-4 py-3">
                            <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/task/toggle" class="flex-shrink-0">
                                <input type="hidden" name="task_id" value="<?= htmlspecialchars((string)$task['id']) ?>">
                                <input type="hidden" name="plan_id" value="<?= htmlspecialchars((string)$plan['id']) ?>">
                                <button type="submit"
                                        class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors
                                               <?= $task['status'] === 'done' ? 'bg-green-600 border-green-600' : 'border-slate-500 hover:border-blue-500' ?>">
                                    <?php if ($task['status'] === 'done'): ?>
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <?php endif; ?>
                                </button>
                            </form>
                            <?php else: ?>
                            <div class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0
                                        <?= $task['status'] === 'done' ? 'bg-green-600 border-green-600' : 'border-slate-500' ?>">
                                <?php if ($task['status'] === 'done'): ?>
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <span class="text-sm <?= $task['status'] === 'done' ? 'line-through text-slate-500' : 'text-slate-300' ?>">
                                <?= htmlspecialchars($task['title']) ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>/task" class="flex gap-2 mt-4">
                        <input type="text" name="title" required placeholder="Add a task..."
                               class="flex-1 bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">Add Task</button>
                    </form>
                    <?php endif; ?>
                </div>

                <!-- Update Status (admin/dev) -->
                <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Update Plan Status</h3>
                    <div class="flex gap-3 flex-wrap">
                        <?php foreach (['pending', 'in_progress', 'completed'] as $s): ?>
                        <span class="text-xs px-3 py-1.5 rounded border <?= $plan['status'] === $s ? 'bg-blue-600 text-white border-blue-500' : 'border-slate-600 text-slate-400' ?>">
                            <?= ucfirst(str_replace('_', ' ', $s)) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>
