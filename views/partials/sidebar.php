<?php
// views/partials/sidebar.php
$currentUser = $currentUser ?? $user ?? [];
$role = $currentUser['role'] ?? 'user';
$appUrl = $appUrl ?? $_ENV['APP_URL'] ?? '';
?>
<aside class="w-64 bg-slate-800 border-r border-slate-700 flex flex-col">
    <div class="p-4 border-b border-slate-700">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-lg font-bold text-white">HelpDesk</span>
        </div>
        <p class="text-xs text-slate-400 mt-1">CometaxCompany</p>
    </div>

    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <a href="<?= htmlspecialchars($appUrl) ?>/tickets/list"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
            <span>Tickets</span>
        </a>

        <a href="<?= htmlspecialchars($appUrl) ?>/tickets/create"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>New Ticket</span>
        </a>

        <a href="<?= htmlspecialchars($appUrl) ?>/knowledge"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Knowledge Base</span>
        </a>

        <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Weekly Plans</span>
        </a>

        <?php if ($role === 'admin'): ?>
        <div class="pt-2 mt-2 border-t border-slate-700">
            <p class="px-3 py-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</p>
            <a href="<?= htmlspecialchars($appUrl) ?>/admin/users"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors mt-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Users</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <div class="p-4 border-t border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-700 rounded-full flex items-center justify-center text-sm font-bold text-white">
                <?= htmlspecialchars(strtoupper(substr($currentUser['name'] ?? 'U', 0, 1))) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($currentUser['name'] ?? '') ?></p>
                <p class="text-xs text-slate-400 capitalize"><?= htmlspecialchars($role) ?></p>
            </div>
            <a href="<?= htmlspecialchars($appUrl) ?>/logout" title="Logout"
               class="text-slate-400 hover:text-red-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </a>
        </div>
    </div>
</aside>
