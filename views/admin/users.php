<?php $pageTitle = 'Users'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">
                        User Management <span class="text-slate-400 text-sm font-normal">(<?= count($users) ?>)</span>
                    </h2>
                    <a href="<?= htmlspecialchars($appUrl) ?>/admin/users/create"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        + Add User
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-700/50 text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-left">Role</th>
                                <th class="px-4 py-3 text-left">Created</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars((string)$u['id']) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-700 rounded-full flex items-center justify-center text-xs font-bold text-white">
                                            <?= htmlspecialchars(strtoupper(substr($u['name'], 0, 1))) ?>
                                        </div>
                                        <span class="text-white font-medium"><?= htmlspecialchars($u['name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-400"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                    $roleBadge = match($u['role']) {
                                        'admin' => 'bg-red-900/50 text-red-300 border-red-700',
                                        'dev'   => 'bg-blue-900/50 text-blue-300 border-blue-700',
                                        default => 'bg-slate-700 text-slate-300 border-slate-600',
                                    };
                                    ?>
                                    <span class="text-xs px-2 py-1 rounded border capitalize <?= $roleBadge ?>"><?= htmlspecialchars($u['role']) ?></span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs"><?= htmlspecialchars(date('M j, Y', strtotime($u['created_at']))) ?></td>
                                <td class="px-4 py-3">
                                    <?php if ((int)$u['id'] !== $user['id']): ?>
                                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/users/<?= htmlspecialchars((string)$u['id']) ?>/delete"
                                          onsubmit="return confirm('Delete user <?= htmlspecialchars(addslashes($u['name'])) ?>?')">
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">Delete</button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-slate-600 text-xs">You</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
