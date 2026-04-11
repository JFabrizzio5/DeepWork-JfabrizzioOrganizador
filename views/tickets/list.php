<?php
$pageTitle = 'Tickets';
$statusColors = [
    'new'         => 'bg-blue-900/50 text-blue-300 border-blue-700',
    'in_progress' => 'bg-yellow-900/50 text-yellow-300 border-yellow-700',
    'review'      => 'bg-purple-900/50 text-purple-300 border-purple-700',
    'done'        => 'bg-green-900/50 text-green-300 border-green-700',
];
$impactColors = [
    'low'      => 'text-green-400',
    'medium'   => 'text-yellow-400',
    'high'     => 'text-orange-400',
    'critical' => 'text-red-400',
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

            <!-- Filters -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 mb-6">
                <form method="GET" action="<?= htmlspecialchars($appUrl) ?>/tickets/list" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Status</label>
                        <select name="status" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">All Status</option>
                            <?php foreach (['new', 'in_progress', 'review', 'done'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Type</label>
                        <select name="type" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">All Types</option>
                            <?php foreach (['bug', 'feature', 'support', 'query'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($filters['type'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Impact</label>
                        <select name="impact" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">All Impact</option>
                            <?php foreach (['low', 'medium', 'high', 'critical'] as $i): ?>
                            <option value="<?= $i ?>" <?= ($filters['impact'] ?? '') === $i ? 'selected' : '' ?>><?= ucfirst($i) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">Filter</button>
                    <a href="<?= htmlspecialchars($appUrl) ?>/tickets/list" class="text-slate-400 hover:text-white text-sm px-3 py-2">Clear</a>
                </form>
            </div>

            <!-- Ticket Table -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">
                        All Tickets <span class="text-slate-400 text-sm font-normal">(<?= count($tickets) ?>)</span>
                    </h2>
                    <a href="<?= htmlspecialchars($appUrl) ?>/tickets/create" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">+ New Ticket</a>
                </div>

                <?php if (empty($tickets)): ?>
                <div class="p-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-lg font-medium">No tickets found</p>
                    <p class="text-sm mt-1">Create your first ticket to get started</p>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-700/50 text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">#ID</th>
                                <th class="px-4 py-3 text-left">Title / Description</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Impact</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Assigned</th>
                                <th class="px-4 py-3 text-left">Created</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php foreach ($tickets as $ticket): ?>
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="px-4 py-3 text-slate-400 font-mono">#<?= htmlspecialchars((string)$ticket['id']) ?></td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-white truncate max-w-xs">
                                        <?= htmlspecialchars($ticket['title'] ?: substr($ticket['description'], 0, 60) . '...') ?>
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($ticket['requester_email']) ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded capitalize"><?= htmlspecialchars($ticket['type']) ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-medium capitalize <?= $impactColors[$ticket['impact']] ?? 'text-slate-400' ?>">
                                        <?= htmlspecialchars($ticket['impact']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-1 rounded border capitalize <?= $statusColors[$ticket['status']] ?? 'bg-slate-700 text-slate-300 border-slate-600' ?>">
                                        <?= htmlspecialchars(str_replace('_', ' ', $ticket['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-xs">
                                    <?= htmlspecialchars($ticket['assigned_name'] ?? 'Unassigned') ?>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs">
                                    <?= htmlspecialchars(date('M j, Y', strtotime($ticket['created_at']))) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>"
                                       class="text-blue-400 hover:text-blue-300 text-xs font-medium">View →</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>
