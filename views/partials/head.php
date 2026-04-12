<?php
// views/partials/head.php
// Include inside <head> on every page. Provides: theme-init script, Tailwind CDN, light-mode overrides.
?>
<script>
/* Apply saved theme BEFORE the page renders to avoid a flash */
(function () {
    var saved = localStorage.getItem('theme');
    var systemLight = !window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (saved === 'light' || (!saved && systemLight)) {
        document.documentElement.classList.add('light');
    }
}());
</script>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* ── Light-mode colour overrides ───────────────────────────────────────
   Default (no class) = dark theme (existing Tailwind classes unchanged).
   html.light = light theme. Higher specificity wins without !important.   */

html.light body                 { background-color:#f0f4f8; color:#1e293b; }
html.light .bg-slate-900        { background-color:#f0f4f8; }
html.light .bg-slate-800        { background-color:#ffffff; }
html.light .bg-slate-700        { background-color:#e2e8f0; }

/* Slash-opacity variants (Tailwind generates e.g. .bg-slate-700\/50) */
html.light .bg-slate-700\/50    { background-color:rgba(203,213,225,.5) !important; }
html.light .bg-slate-700\/30    { background-color:rgba(203,213,225,.3) !important; }
html.light .bg-black\/60        { background-color:rgba(0,0,0,.4)       !important; }
html.light .bg-slate-900\/50    { background-color:rgba(226,232,240,.5) !important; }

/* Text colours */
html.light .text-white          { color:#0f172a; }
html.light .text-slate-100      { color:#1e293b; }
html.light .text-slate-300      { color:#334155; }
html.light .text-slate-400      { color:#64748b; }
html.light .text-slate-500      { color:#94a3b8; }
html.light .text-slate-600      { color:#64748b; }

/* Borders & dividers */
html.light .border-slate-700    { border-color:#cbd5e1; }
html.light .border-slate-600    { border-color:#94a3b8; }
html.light .divide-slate-700 > :not([hidden]) ~ :not([hidden]) { border-color:#cbd5e1; }

/* Hover states */
html.light .hover\:bg-slate-700:hover           { background-color:#e2e8f0; }
html.light .hover\:bg-slate-600:hover           { background-color:#cbd5e1; }
html.light .hover\:bg-slate-700\/30:hover       { background-color:rgba(203,213,225,.3); }
html.light .hover\:text-white:hover             { color:#0f172a; }

/* Form elements */
html.light select,
html.light input[type=text],
html.light input[type=email],
html.light input[type=password],
html.light input[type=date],
html.light textarea              { background-color:#e2e8f0; color:#0f172a; border-color:#94a3b8; }
html.light .placeholder-slate-400::placeholder  { color:#94a3b8; }

/* Table header row */
html.light .bg-slate-700\/50 th,
html.light thead                { background-color:rgba(203,213,225,.5); }

/* Scrollbars (WebKit) */
html.light ::-webkit-scrollbar-track  { background:#f0f4f8; }
html.light ::-webkit-scrollbar-thumb  { background:#cbd5e1; }

/* Elevation in light mode */
html.light .shadow-xl           { box-shadow:0 20px 25px -5px rgba(0,0,0,.08),0 8px 10px -6px rgba(0,0,0,.06); }
</style>
