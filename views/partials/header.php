<?php
// views/partials/header.php
$pageTitle = $pageTitle ?? 'HelpDesk';
$appUrl = $appUrl ?? $_ENV['APP_URL'] ?? '';
?>
<header class="bg-slate-800 border-b border-slate-700 px-6 py-3 flex items-center justify-between">
    <h1 class="text-lg font-semibold text-white"><?= htmlspecialchars($pageTitle) ?></h1>
    <div class="flex items-center gap-4">
        <a href="<?= htmlspecialchars($appUrl) ?>/tickets/create"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Ticket
        </a>
    </div>
</header>
