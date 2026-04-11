<?php $pageTitle = 'Create Ticket'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ticket | HelpDesk</title>
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
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white">Create New Ticket</h2>
                    <p class="text-slate-400 mt-1">Submit a support request, bug report, or feature request.</p>
                </div>

                <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/store" enctype="multipart/form-data" class="space-y-6">

                    <!-- Basic Info -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Title <span class="text-slate-500">(optional)</span></label>
                                <input type="text" name="title" maxlength="200"
                                       class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                       placeholder="Brief summary of the issue">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Description <span class="text-red-400">*</span></label>
                                <textarea name="description" rows="5" required
                                          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"
                                          placeholder="Describe the issue in detail..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Classification -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Classification</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                                <select name="type" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                    <option value="support">Support</option>
                                    <option value="bug">Bug</option>
                                    <option value="feature">Feature</option>
                                    <option value="query">Query</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Impact</label>
                                <select name="impact" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Priority</label>
                                <select name="priority_user" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Details -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Technical Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Steps to Reproduce</label>
                                <textarea name="steps_to_reproduce" rows="3"
                                          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"
                                          placeholder="1. Go to...&#10;2. Click on...&#10;3. See error"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Technical Context</label>
                                <textarea name="technical_context" rows="2"
                                          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"
                                          placeholder="Environment, version, OS, browser..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Requester Info -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Requester Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                                <input type="text" name="requester_name" value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                       class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                       placeholder="Your name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Email <span class="text-red-400">*</span></label>
                                <input type="email" name="requester_email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                       class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                       placeholder="email@example.com">
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-1">Evidence / Attachments</h3>
                        <p class="text-xs text-slate-500 mb-4">Allowed: PNG, JPG, PDF, XML, ZIP, MP4. Multiple files supported.</p>
                        <input type="file" name="evidence[]" multiple accept=".png,.jpg,.jpeg,.pdf,.xml,.zip,.mp4"
                               class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                            Submit Ticket
                        </button>
                        <a href="<?= htmlspecialchars($appUrl) ?>/tickets/list" class="text-slate-400 hover:text-white transition-colors">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>
