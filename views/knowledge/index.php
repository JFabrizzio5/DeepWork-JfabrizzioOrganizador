<?php
$pageTitle = 'Knowledge Base';
$tagTypeColors = [
    'documentation' => 'bg-blue-900/40 text-blue-300',
    'template'      => 'bg-purple-900/40 text-purple-300',
    'plan'          => 'bg-green-900/40 text-green-300',
    'weekly_status' => 'bg-yellow-900/40 text-yellow-300',
    'repository'    => 'bg-orange-900/40 text-orange-300',
];
$searchQuery = $searchQuery ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Base | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <!-- Header + Search -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white">Knowledge Base</h2>
                    <p class="text-slate-400 text-sm mt-1">Templates, documentation, and references.</p>
                </div>
                <div class="flex gap-3">
                    <form method="GET" action="<?= htmlspecialchars($appUrl) ?>/knowledge/search" class="flex gap-2">
                        <input type="search" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search articles..."
                               class="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 w-48">
                        <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white text-sm px-3 py-2 rounded-lg transition-colors">Search</button>
                    </form>
                    <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= htmlspecialchars($appUrl) ?>/knowledge/create"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors whitespace-nowrap">+ New Article</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="<?= htmlspecialchars($appUrl) ?>/knowledge"
                   class="text-xs px-3 py-1.5 rounded-lg transition-colors <?= empty($filters['tag_type'] ?? '') ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' ?>">All</a>
                <?php foreach (['documentation', 'template', 'plan', 'weekly_status', 'repository'] as $tt): ?>
                <a href="<?= htmlspecialchars($appUrl) ?>/knowledge?tag_type=<?= $tt ?>"
                   class="text-xs px-3 py-1.5 rounded-lg transition-colors capitalize <?= ($filters['tag_type'] ?? '') === $tt ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' ?>">
                    <?= ucfirst(str_replace('_', ' ', $tt)) ?>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($searchQuery)): ?>
            <p class="text-slate-400 text-sm mb-4">Search results for "<strong class="text-white"><?= htmlspecialchars($searchQuery) ?></strong>" — <?= count($articles) ?> found</p>
            <?php endif; ?>

            <!-- Articles Grid -->
            <?php if (empty($articles)): ?>
            <div class="text-center py-16 text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-lg font-medium">No articles found</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php foreach ($articles as $article): ?>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-5 hover:border-blue-600 transition-colors">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="text-white font-semibold leading-snug">
                            <a href="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>"
                               class="hover:text-blue-400 transition-colors">
                                <?= htmlspecialchars($article['title']) ?>
                            </a>
                        </h3>
                        <span class="text-xs px-2 py-0.5 rounded capitalize whitespace-nowrap flex-shrink-0 <?= $tagTypeColors[$article['tag_type']] ?? 'bg-slate-700 text-slate-300' ?>">
                            <?= htmlspecialchars(str_replace('_', ' ', $article['tag_type'])) ?>
                        </span>
                    </div>
                    <p class="text-slate-400 text-sm line-clamp-3 mb-4">
                        <?= htmlspecialchars(substr($article['content'], 0, 150)) ?>...
                    </p>
                    <?php if (!empty($article['tags'])): ?>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <?php foreach (explode(',', $article['tags']) as $tag): ?>
                        <span class="text-xs bg-slate-700/60 text-slate-400 px-2 py-0.5 rounded"><?= htmlspecialchars(trim($tag)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span><?= htmlspecialchars($article['creator_name'] ?? 'Unknown') ?></span>
                        <span><?= htmlspecialchars(date('M j, Y', strtotime($article['created_at']))) ?></span>
                    </div>
                    <?php if ($user['role'] === 'admin'): ?>
                    <div class="mt-3 pt-3 border-t border-slate-700 flex gap-3">
                        <a href="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>" class="text-blue-400 hover:text-blue-300 text-xs">View</a>
                        <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/knowledge/<?= htmlspecialchars((string)$article['id']) ?>/delete"
                              onsubmit="return confirm('Delete this article?')" class="inline">
                            <button type="submit" class="text-red-400 hover:text-red-300 text-xs">Delete</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
