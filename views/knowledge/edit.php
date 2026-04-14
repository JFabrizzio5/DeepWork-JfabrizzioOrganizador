<?php $pageTitle = 'Edit Article'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article | HelpDesk</title>
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
                    <a href="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>" class="text-slate-400 hover:text-white text-sm transition-colors">← Back to Article</a>
                    <h2 class="text-2xl font-bold text-white mt-2">Edit Article</h2>
                </div>

                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>/update" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Title <span class="text-red-400">*</span></label>
                            <input type="text" name="title" required maxlength="200"
                                   value="<?= htmlspecialchars($article['title']) ?>"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                   placeholder="Article title">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                            <select name="tag_type" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                <?php foreach (['documentation', 'template', 'plan', 'weekly_status', 'repository'] as $tt): ?>
                                <option value="<?= $tt ?>" <?= ($article['tag_type'] ?? '') === $tt ? 'selected' : '' ?>>
                                    <?= ucfirst(str_replace('_', ' ', $tt)) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Content <span class="text-red-400">*</span></label>
                            <textarea name="content" rows="12" required
                                      class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-y font-mono text-sm"
                                      placeholder="Write your article content here..."><?= htmlspecialchars($article['content']) ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Tags <span class="text-slate-500">(comma-separated)</span></label>
                            <input type="text" name="tags"
                                   value="<?= htmlspecialchars($article['tags'] ?? '') ?>"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                   placeholder="php, bug, template">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Links <span class="text-slate-500">(URLs, one per line)</span></label>
                            <textarea name="links" rows="3"
                                      class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none text-sm"
                                      placeholder="https://docs.example.com&#10;https://github.com/..."><?= htmlspecialchars($article['links'] ?? '') ?></textarea>
                        </div>

                        <?php if (!empty($files)): ?>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Current Attachments</label>
                            <div class="space-y-2">
                                <?php foreach ($files as $file): ?>
                                <div class="flex items-center gap-3 bg-slate-900/50 rounded-lg px-4 py-3">
                                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-slate-300 truncate"><?= htmlspecialchars($file['original_name']) ?></p>
                                        <p class="text-xs text-slate-500">
                                            <?= htmlspecialchars(strtoupper($file['file_type'] ?? '')) ?>
                                            · <?= number_format(($file['file_size'] ?? 0) / 1024, 1) ?> KB
                                        </p>
                                    </div>
                                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>/file/<?= htmlspecialchars((string)$file['id']) ?>/delete"
                                          onsubmit="return confirm('Delete this file?')" class="flex-shrink-0">
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs px-3 py-1 bg-slate-700 rounded transition-colors">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Add Attachments <span class="text-slate-500">(.zip, .pdf, images, etc.)</span></label>
                            <input type="file" name="attachments[]" multiple
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 focus:outline-none focus:border-blue-500">
                            <p class="text-xs text-slate-500 mt-1">You can select multiple files. Allowed: png, jpg, jpeg, gif, pdf, doc, docx, xls, xlsx, ppt, pptx, txt, csv, xml, zip, rar, 7z, tar, gz, mp4, mp3, json, sql, md, html, css, js, php, py</p>
                        </div>
                        <div class="pt-2 flex gap-3">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                                Update Article
                            </button>
                            <a href="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>" class="text-slate-400 hover:text-white px-4 py-2.5 transition-colors">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
