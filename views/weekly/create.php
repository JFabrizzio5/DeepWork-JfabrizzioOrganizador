<?php $pageTitle = 'Create Weekly Plan'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Weekly Plan | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="max-w-2xl mx-auto">
                <div class="mb-6">
                    <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan" class="text-slate-400 hover:text-white text-sm transition-colors">← Back to Weekly Plans</a>
                    <h2 class="text-2xl font-bold text-white mt-2">New Weekly Plan</h2>
                </div>

                <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/store" enctype="multipart/form-data" class="space-y-6">

                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Plan Details</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Week Start Date <span class="text-red-400">*</span></label>
                                    <input type="date" name="week_start" required
                                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Project</label>
                                    <select name="project" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                        <?php foreach (['A', 'B', 'C', 'D'] as $p): ?>
                                        <option value="<?= $p ?>">Project <?= $p ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Assign to</label>
                                <select name="assigned_to" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($allUsers as $u): ?>
                                    <option value="<?= htmlspecialchars((string)$u['id']) ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['role']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Summary</label>
                                <textarea name="summary" rows="4"
                                          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"
                                          placeholder="Brief description of the week's objectives..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Initial Tasks</h3>
                        <p class="text-slate-400 text-sm mb-4">Add tasks for this week (you can add more later).</p>
                        <div id="tasks-container" class="space-y-2">
                            <div class="flex gap-2">
                                <input type="text" name="tasks[]" placeholder="Task description..."
                                       class="flex-1 bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm">
                                <button type="button" onclick="addTask()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- File -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-1">Attach Plan File</h3>
                        <p class="text-xs text-slate-500 mb-4">Optional. Allowed: PDF, Excel, Word documents.</p>
                        <input type="file" name="plan_file" accept=".pdf,.xlsx,.xls,.doc,.docx"
                               class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                            Create Plan
                        </button>
                        <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan" class="text-slate-400 hover:text-white px-4 py-3 transition-colors">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<script>
function addTask() {
    const container = document.getElementById('tasks-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = '<input type="text" name="tasks[]" placeholder="Task description..." class="flex-1 bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm">' +
        '<button type="button" onclick="this.parentElement.remove()" class="bg-red-700 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">×</button>';
    container.appendChild(div);
}
</script>
</body>
</html>
