<?php
$pageTitle = htmlspecialchars($article['title'] ?? 'Article');
$tagTypeColors = [
    'documentation' => 'bg-blue-900/40 text-blue-300',
    'template'      => 'bg-purple-900/40 text-purple-300',
    'plan'          => 'bg-green-900/40 text-green-300',
    'weekly_status' => 'bg-yellow-900/40 text-yellow-300',
    'repository'    => 'bg-orange-900/40 text-orange-300',
];
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">

            <div class="max-w-3xl mx-auto">
                <div class="mb-6 flex items-center justify-between">
                    <a href="<?= htmlspecialchars($appUrl) ?>/knowledge" class="text-slate-400 hover:text-white text-sm transition-colors">← Back to Knowledge Base</a>
                    <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                    <div class="flex gap-3">
                        <a href="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>/edit"
                           class="text-blue-400 hover:text-blue-300 text-sm transition-colors">Edit Article</a>
                        <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>/delete"
                              onsubmit="return confirm('Delete this article?')">
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition-colors">Delete Article</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="bg-slate-800 rounded-xl border border-slate-700 p-8">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-xs px-2 py-1 rounded capitalize <?= $tagTypeColors[$article['tag_type']] ?? 'bg-slate-700 text-slate-300' ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', $article['tag_type'])) ?>
                                </span>
                            </div>
                            <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($article['title']) ?></h1>
                            <p class="text-slate-500 text-sm mt-2">
                                By <?= htmlspecialchars($article['creator_name'] ?? 'Unknown') ?>
                                · <?= htmlspecialchars(date('M j, Y', strtotime($article['created_at']))) ?>
                                <?php if ($article['updated_at'] !== $article['created_at']): ?>
                                · Updated <?= htmlspecialchars(date('M j, Y', strtotime($article['updated_at']))) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($article['tags'])): ?>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php foreach (explode(',', $article['tags']) as $tag): ?>
                        <span class="text-xs bg-slate-700/60 text-slate-400 px-2 py-1 rounded"><?= htmlspecialchars(trim($tag)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="prose-dark">
                        <div class="bg-slate-900/50 rounded-lg p-6 text-slate-300 text-sm leading-relaxed whitespace-pre-wrap">
                            <?= htmlspecialchars($article['content']) ?>
                        </div>
                    </div>

                    <?php if (!empty($article['links'])): ?>
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-3">Related Links</h3>
                        <ul class="space-y-1">
                            <?php foreach (explode("\n", $article['links']) as $link): ?>
                            <?php $link = trim($link); if (empty($link)) continue; ?>
                            <li>
                                <a href="<?= htmlspecialchars($link) ?>" target="_blank" rel="noopener noreferrer"
                                   class="text-blue-400 hover:text-blue-300 text-sm transition-colors break-all">
                                    <?= htmlspecialchars($link) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($files)): ?>
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-3">Attachments (<?= count($files) ?>)</h3>
                        <div class="space-y-2">
                            <?php foreach ($files as $file): ?>
                            <div class="flex items-center gap-3 bg-slate-900/50 rounded-lg px-4 py-3">
                                <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <a href="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>/file/<?= htmlspecialchars((string)$file['id']) ?>"
                                       class="text-blue-400 hover:text-blue-300 text-sm transition-colors truncate block">
                                        <?= htmlspecialchars($file['original_name']) ?>
                                    </a>
                                    <p class="text-xs text-slate-500">
                                        <?= htmlspecialchars(strtoupper($file['file_type'] ?? '')) ?>
                                        · <?= number_format(($file['file_size'] ?? 0) / 1024, 1) ?> KB
                                        <?php if (!empty($file['uploader_name'])): ?>
                                        · Uploaded by <?= htmlspecialchars($file['uploader_name']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <a href="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>/file/<?= htmlspecialchars((string)$file['id']) ?>"
                                   class="text-slate-400 hover:text-white text-xs px-3 py-1 bg-slate-700 rounded transition-colors flex-shrink-0">
                                    Download
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
