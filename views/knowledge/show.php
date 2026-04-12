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
                    <?php if ($user['role'] === 'admin'): ?>
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>/delete"
                          onsubmit="return confirm('Delete this article?')">
                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition-colors">Delete Article</button>
                    </form>
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
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
