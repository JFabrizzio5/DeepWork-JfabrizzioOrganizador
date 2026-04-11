<?php
$pageTitle = 'Weekly Plans';
$statusColors = [
    'pending'     => 'bg-slate-700 text-slate-300 border-slate-600',
    'in_progress' => 'bg-yellow-900/50 text-yellow-300 border-yellow-700',
    'completed'   => 'bg-green-900/50 text-green-300 border-green-700',
];
$projectColors = [
    'A' => 'bg-blue-700 text-white',
    'B' => 'bg-purple-700 text-white',
    'C' => 'bg-green-700 text-white',
    'D' => 'bg-orange-700 text-white',
];
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Plans | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white">Weekly Plans</h2>
                    <p class="text-slate-400 text-sm mt-1">Track weekly work progress by project.</p>
                </div>
                <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan/create"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                    + New Plan
                </a>
                <?php endif; ?>
            </div>

            <!-- Filters -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 mb-6">
                <form method="GET" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Project</label>
                        <select name="project" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">All Projects</option>
                            <?php foreach (['A', 'B', 'C', 'D'] as $p): ?>
                            <option value="<?= $p ?>" <?= ($filters['project'] ?? '') === $p ? 'selected' : '' ?>>Project <?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Status</label>
                        <select name="status" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">All Status</option>
                            <?php foreach (['pending', 'in_progress', 'completed'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Developer</label>
                        <select name="assigned_to" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">All Developers</option>
                            <?php foreach ($developers as $dev): ?>
                            <option value="<?= htmlspecialchars((string)$dev['id']) ?>" <?= ($filters['assigned_to'] ?? '') == $dev['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dev['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">Filter</button>
                    <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan" class="text-slate-400 hover:text-white text-sm px-3 py-2">Clear</a>
                </form>
            </div>

            <?php if (empty($plans)): ?>
            <div class="text-center py-16 text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-lg font-medium">No weekly plans found</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php foreach ($plans as $plan): ?>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-5 hover:border-blue-600 transition-colors">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold px-2 py-0.5 rounded <?= $projectColors[$plan['project']] ?? 'bg-slate-700 text-white' ?>">
                                    Project <?= htmlspecialchars($plan['project']) ?>
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded border <?= $statusColors[$plan['status']] ?? 'bg-slate-700 text-slate-300 border-slate-600' ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', $plan['status'])) ?>
                                </span>
                            </div>
                            <p class="text-white font-semibold">Week of <?= htmlspecialchars(date('M j, Y', strtotime($plan['week_start']))) ?></p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                            <span>Progress</span>
                            <span><?= htmlspecialchars((string)$plan['progress_percent']) ?>%</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: <?= min(100, (int)$plan['progress_percent']) ?>%"></div>
                        </div>
                    </div>

                    <?php if (!empty($plan['summary'])): ?>
                    <p class="text-slate-400 text-sm line-clamp-2 mb-3"><?= htmlspecialchars($plan['summary']) ?></p>
                    <?php endif; ?>

                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span><?= htmlspecialchars($plan['assigned_name'] ?? 'Unassigned') ?></span>
                        <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>"
                           class="text-blue-400 hover:text-blue-300 transition-colors">View →</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
