<?php $pageTitle = $editProject ? 'Edit Project' : 'New Project'; ?>
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

            <div class="max-w-xl mx-auto">
                <div class="mb-6">
                    <a href="<?= htmlspecialchars($appUrl) ?>/admin/projects" class="text-slate-400 hover:text-white text-sm transition-colors">← Back to Projects</a>
                    <h2 class="text-2xl font-bold text-white mt-2"><?= htmlspecialchars($pageTitle) ?></h2>
                </div>

                <?php if (!empty($error)): ?>
                <div class="bg-red-900/40 border border-red-700 text-red-300 rounded-lg px-4 py-3 mb-4 text-sm"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php
                $formAction = $editProject
                    ? $appUrl . '/admin/projects/' . $editProject['id'] . '/update'
                    : $appUrl . '/admin/projects/store';
                ?>
                <form method="POST" action="<?= htmlspecialchars($formAction) ?>" class="space-y-5">
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Project Name <span class="text-red-400">*</span></label>
                            <input type="text" name="name" required
                                   value="<?= htmlspecialchars($editProject['name'] ?? '') ?>"
                                   placeholder="e.g. Backend API"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="color"
                                       value="<?= htmlspecialchars($editProject['color'] ?? '#3B82F6') ?>"
                                       class="w-12 h-10 rounded-lg border border-slate-600 bg-slate-700 cursor-pointer p-1">
                                <span class="text-slate-400 text-sm">Choose a color for this project's badge.</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"
                                      placeholder="Optional description..."><?= htmlspecialchars($editProject['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                            <?= $editProject ? 'Update Project' : 'Create Project' ?>
                        </button>
                        <a href="<?= htmlspecialchars($appUrl) ?>/admin/projects" class="text-slate-400 hover:text-white px-4 py-3 transition-colors">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>
