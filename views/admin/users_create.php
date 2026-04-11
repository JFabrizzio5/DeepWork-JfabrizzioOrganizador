<?php $pageTitle = 'Create User'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="max-w-lg mx-auto">
                <div class="mb-6">
                    <a href="<?= htmlspecialchars($appUrl) ?>/admin/users" class="text-slate-400 hover:text-white text-sm transition-colors">← Back to Users</a>
                    <h2 class="text-2xl font-bold text-white mt-2">Create User</h2>
                </div>

                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/users/store" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Full Name <span class="text-red-400">*</span></label>
                            <input type="text" name="name" required
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                   placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Email <span class="text-red-400">*</span></label>
                            <input type="email" name="email" required
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                   placeholder="john@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Password <span class="text-red-400">*</span></label>
                            <input type="password" name="password" required minlength="6"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                   placeholder="Min. 6 characters">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Role</label>
                            <select name="role" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                <option value="user">User</option>
                                <option value="dev">Developer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="pt-2 flex gap-3">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                                Create User
                            </button>
                            <a href="<?= htmlspecialchars($appUrl) ?>/admin/users" class="text-slate-400 hover:text-white px-4 py-2.5 transition-colors">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
