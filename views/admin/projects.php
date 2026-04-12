<?php $pageTitle = 'Projects'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white">Projects</h2>
                    <p class="text-slate-400 text-sm mt-1">Manage projects used in weekly plans.</p>
                </div>
                <a href="<?= htmlspecialchars($appUrl) ?>/admin/projects/create"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                    + New Project
                </a>
            </div>

            <?php if (empty($projects)): ?>
            <div class="text-center py-16 text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                <p class="text-lg font-medium">No projects yet</p>
            </div>
            <?php else: ?>
            <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-700 text-slate-400 text-xs uppercase tracking-wider">
                            <th class="px-5 py-3 text-left">Color</th>
                            <th class="px-5 py-3 text-left">Name</th>
                            <th class="px-5 py-3 text-left">Description</th>
                            <th class="px-5 py-3 text-left">Created</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        <?php foreach ($projects as $project): ?>
                        <tr class="hover:bg-slate-700/40 transition-colors">
                            <td class="px-5 py-3">
                                <span class="inline-block w-6 h-6 rounded-full border border-slate-600"
                                      style="background-color: <?= htmlspecialchars($project['color']) ?>"></span>
                            </td>
                            <td class="px-5 py-3 font-medium text-white"><?= htmlspecialchars($project['name']) ?></td>
                            <td class="px-5 py-3 text-slate-400 max-w-xs truncate"><?= htmlspecialchars($project['description'] ?? '') ?></td>
                            <td class="px-5 py-3 text-slate-400"><?= htmlspecialchars(date('M j, Y', strtotime($project['created_at']))) ?></td>
                            <td class="px-5 py-3 text-right flex items-center justify-end gap-3">
                                <a href="<?= htmlspecialchars($appUrl) ?>/admin/projects/<?= htmlspecialchars((string)$project['id']) ?>/edit"
                                   class="text-blue-400 hover:text-blue-300 transition-colors">Edit</a>
                                <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/projects/<?= htmlspecialchars((string)$project['id']) ?>/delete"
                                      onsubmit="return confirm('Delete this project?')">
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
