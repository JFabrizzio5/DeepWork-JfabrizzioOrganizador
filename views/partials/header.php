<?php
// views/partials/header.php
$pageTitle = $pageTitle ?? 'HelpDesk';
$appUrl = $appUrl ?? $_ENV['APP_URL'] ?? '';
?>
<header class="bg-slate-800 border-b border-slate-700 px-4 py-3 flex items-center justify-between gap-3 flex-shrink-0">
    <div class="flex items-center gap-3 min-w-0">
        <!-- Hamburger – mobile only -->
        <button onclick="toggleSidebar()"
                class="lg:hidden text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-700 transition-colors flex-shrink-0"
                aria-label="Abrir menú">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <h1 class="text-base sm:text-lg font-semibold text-white truncate"><?= htmlspecialchars($pageTitle) ?></h1>
    </div>

    <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
        <!-- Dark / Light toggle -->
        <button id="theme-toggle"
                onclick="toggleTheme()"
                title="Cambiar tema"
                class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-700 transition-colors">
            <!-- Sun (visible in dark mode) -->
            <svg id="icon-sun" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M18.364 18.364l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <!-- Moon (visible in light mode) -->
            <svg id="icon-moon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>

        <!-- New ticket shortcut -->
        <a href="<?= htmlspecialchars($appUrl) ?>/tickets/create"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 sm:px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline whitespace-nowrap">Nuevo Ticket</span>
        </a>
    </div>
</header>

<script>
/* ── Sidebar toggle ─────────────────────────────────── */
function openSidebar() {
    var s = document.getElementById('sidebar');
    var o = document.getElementById('sidebar-overlay');
    if (!s || !o) return;
    s.classList.remove('-translate-x-full');
    s.classList.add('translate-x-0');
    o.classList.remove('hidden');
}
function closeSidebar() {
    var s = document.getElementById('sidebar');
    var o = document.getElementById('sidebar-overlay');
    if (!s || !o) return;
    s.classList.add('-translate-x-full');
    s.classList.remove('translate-x-0');
    o.classList.add('hidden');
}
function toggleSidebar() {
    var s = document.getElementById('sidebar');
    if (!s) return;
    if (s.classList.contains('-translate-x-full')) { openSidebar(); } else { closeSidebar(); }
}

/* ── Theme toggle ───────────────────────────────────── */
function toggleTheme() {
    var isLight = document.documentElement.classList.toggle('light');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
    updateThemeIcons();
}
function updateThemeIcons() {
    var isLight = document.documentElement.classList.contains('light');
    var sun  = document.getElementById('icon-sun');
    var moon = document.getElementById('icon-moon');
    if (sun)  sun.classList.toggle('hidden',  isLight);
    if (moon) moon.classList.toggle('hidden', !isLight);
}
/* Run once DOM is ready */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', updateThemeIcons);
} else {
    updateThemeIcons();
}
</script>

