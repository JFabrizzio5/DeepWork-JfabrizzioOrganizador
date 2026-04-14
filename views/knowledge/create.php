<?php $pageTitle = 'Create Article'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Article | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="max-w-2xl mx-auto">
                <div class="mb-6">
                    <a href="<?= htmlspecialchars($appUrl) ?>/knowledge" class="text-slate-400 hover:text-white text-sm transition-colors">← Back to Knowledge Base</a>
                    <h2 class="text-2xl font-bold text-white mt-2">New Article</h2>
                </div>

                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/knowledge/store" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Title <span class="text-red-400">*</span></label>
                            <input type="text" name="title" required maxlength="200"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                   placeholder="Article title">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                            <select name="tag_type" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                <option value="documentation">Documentation</option>
                                <option value="template">Template</option>
                                <option value="plan">Plan</option>
                                <option value="weekly_status">Weekly Status</option>
                                <option value="repository">Repository</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Content <span class="text-red-400">*</span></label>
                            <textarea name="content" rows="12" required
                                      class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-y font-mono text-sm"
                                      placeholder="Write your article content here..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Tags <span class="text-slate-500">(comma-separated)</span></label>
                            <input type="text" name="tags"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                   placeholder="php, bug, template">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Links <span class="text-slate-500">(URLs, one per line)</span></label>
                            <textarea name="links" rows="3"
                                      class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none text-sm"
                                      placeholder="https://docs.example.com&#10;https://github.com/..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Attachments <span class="text-slate-500">(.zip, .pdf, images, etc.)</span></label>
                            <input type="file" name="attachments[]" multiple
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 focus:outline-none focus:border-blue-500">
                            <p class="text-xs text-slate-500 mt-1">You can select multiple files. Allowed: png, jpg, jpeg, gif, pdf, doc, docx, xls, xlsx, ppt, pptx, txt, csv, xml, zip, rar, 7z, tar, gz, mp4, mp3, json, sql, md, html, css, js, php, py</p>
                        </div>
                        <div class="pt-2 flex gap-3">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                                Publish Article
                            </button>
                            <a href="<?= htmlspecialchars($appUrl) ?>/knowledge" class="text-slate-400 hover:text-white px-4 py-2.5 transition-colors">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
