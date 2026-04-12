<?php
$pageTitle = 'Weekly Plans';
$statusColors = [
    'pending'     => 'bg-slate-700 text-slate-300 border-slate-600',
    'in_progress' => 'bg-yellow-900/50 text-yellow-300 border-yellow-700',
    'completed'   => 'bg-green-900/50 text-green-300 border-green-700',
];
$statusLabels = [
    'pending'     => 'Pendiente',
    'in_progress' => 'En progreso',
    'completed'   => 'Completado',
];

// Build a color map keyed by project name
$projectColorMap = [];
foreach ($projects as $p) {
    $projectColorMap[$p['name']] = $p['color'];
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Plans | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white">Weekly Plans</h2>
                    <p class="text-slate-400 text-sm mt-1">Track weekly work progress by project.</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                    <label class="flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white text-sm px-4 py-2 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Excel
                        <input type="file" id="import-excel-input" accept=".xlsx,.xls,.csv" class="hidden">
                    </label>
                    <button onclick="exportExcel()" class="flex items-center gap-2 bg-green-700 hover:bg-green-600 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        Export Excel
                    </button>
                    <button onclick="exportPDF()" class="flex items-center gap-2 bg-red-700 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Export PDF
                    </button>
                    <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan/create"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        + New Plan
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Import Modal -->
            <div id="import-modal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 w-full max-w-lg mx-4">
                    <h3 class="text-lg font-semibold text-white mb-3">Import Weekly Plan from Excel</h3>
                    <p class="text-slate-400 text-sm mb-4">
                        Your file should have columns: <code class="text-blue-300">week_start</code>,
                        <code class="text-blue-300">project</code>, <code class="text-blue-300">summary</code>,
                        <code class="text-blue-300">task</code> (one task per row).
                    </p>
                    <div id="import-preview" class="bg-slate-900 rounded-lg p-4 mb-4 text-sm text-slate-300 max-h-48 overflow-y-auto hidden"></div>
                    <div id="import-error" class="bg-red-900/40 border border-red-700 text-red-300 rounded-lg px-4 py-3 mb-4 text-sm hidden"></div>
                    <div class="flex gap-3">
                        <button id="import-confirm-btn" onclick="confirmImport()"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2 rounded-lg transition-colors hidden">
                            Import Plan
                        </button>
                        <button onclick="closeImportModal()" class="text-slate-400 hover:text-white text-sm px-4 py-2 transition-colors">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 mb-6">
                <form method="GET" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Proyecto</label>
                        <select name="project" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">Todos los proyectos</option>
                            <?php foreach ($projects as $p): ?>
                            <option value="<?= htmlspecialchars($p['name']) ?>" <?= ($filters['project'] ?? '') === $p['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Estado</label>
                        <select name="status" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="in_progress" <?= ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>En progreso</option>
                            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Desarrollador</label>
                        <select name="assigned_to" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">Todos los desarrolladores</option>
                            <?php foreach ($developers as $dev): ?>
                            <option value="<?= htmlspecialchars((string)$dev['id']) ?>" <?= ($filters['assigned_to'] ?? '') == $dev['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dev['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Semana</label>
                        <input type="date" name="week_start" value="<?= htmlspecialchars($filters['week_start'] ?? '') ?>"
                               class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">Filtrar</button>
                    <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan" class="text-slate-400 hover:text-white text-sm px-3 py-2">Limpiar</a>
                </form>
            </div>

            <?php if (empty($plans)): ?>
            <div class="text-center py-16 text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-lg font-medium">No weekly plans found</p>
            </div>
            <?php else: ?>
            <div id="plans-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php foreach ($plans as $plan): ?>
                <?php $badgeColor = $projectColorMap[$plan['project']] ?? '#3B82F6'; ?>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-5 hover:border-blue-600 transition-colors"
                     data-project="<?= htmlspecialchars($plan['project']) ?>"
                     data-status="<?= htmlspecialchars($plan['status']) ?>"
                     data-week="<?= htmlspecialchars($plan['week_start']) ?>"
                     data-progress="<?= htmlspecialchars((string)$plan['progress_percent']) ?>"
                     data-assigned="<?= htmlspecialchars($plan['assigned_name'] ?? '') ?>">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold px-2 py-0.5 rounded text-white"
                                      style="background-color: <?= htmlspecialchars($badgeColor) ?>">
                                    <?= htmlspecialchars($plan['project']) ?>
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded border <?= $statusColors[$plan['status']] ?? 'bg-slate-700 text-slate-300 border-slate-600' ?>">
                                    <?= $statusLabels[$plan['status']] ?? htmlspecialchars(str_replace('_', ' ', $plan['status'])) ?>
                                </span>
                            </div>
                            <p class="text-white font-semibold">Week of <?= htmlspecialchars(date('M j, Y', strtotime($plan['week_start']))) ?></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                            <span>Progress</span>
                            <span><?= htmlspecialchars((string)$plan['progress_percent']) ?>%</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: <?= min(100, (int)$plan['progress_percent']) ?>%"></div>
                        </div>
                    </div>

                    <?php if (!empty($plan['summary'])): ?>
                    <p class="text-slate-400 text-sm line-clamp-2 mb-3"><?= htmlspecialchars($plan['summary']) ?></p>
                    <?php endif; ?>

                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span><?= htmlspecialchars($plan['assigned_name'] ?? 'Unassigned') ?></span>
                        <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>"
                           class="text-blue-400 hover:text-blue-300 transition-colors">View →</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
const appUrl = <?= json_encode($appUrl) ?>;
let importParsed = null;

document.getElementById('import-excel-input')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (evt) {
        const data = new Uint8Array(evt.target.result);
        const wb   = XLSX.read(data, { type: 'array', cellDates: true });
        const ws   = wb.Sheets[wb.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(ws, { defval: '' });
        parseImportRows(rows, file.name);
    };
    reader.readAsArrayBuffer(file);
    this.value = '';
});

function parseImportRows(rows, filename) {
    if (!rows || rows.length === 0) { showImportError('The file appears to be empty.'); return; }

    const get = (row, candidates) => {
        for (const c of candidates) {
            const k = Object.keys(row).find(k => k.toLowerCase() === c);
            if (k !== undefined && row[k] !== '') return String(row[k]).trim();
        }
        return '';
    };

    let weekStart = '', project = '', summary = '';
    const tasks = [];

    for (const row of rows) {
        const ws  = get(row, ['week_start','week start','semana','date']);
        const prj = get(row, ['project','proyecto']);
        const sum = get(row, ['summary','resumen','description']);
        const tsk = get(row, ['task','tarea','title','titulo']);
        if (ws && !weekStart) weekStart = ws;
        if (prj && !project)  project   = prj;
        if (sum && !summary)  summary   = sum;
        if (tsk)              tasks.push({ title: tsk });
    }

    if (!weekStart) { showImportError('No "week_start" column found. Add a column named week_start, Week Start, or Semana.'); return; }

    const d = new Date(weekStart);
    if (!isNaN(d)) weekStart = d.toISOString().slice(0, 10);

    importParsed = { week_start: weekStart, project, summary, tasks };

    const preview = document.getElementById('import-preview');
    preview.classList.remove('hidden');
    preview.innerHTML = `<strong>File:</strong> ${filename}<br>
        <strong>Week Start:</strong> ${weekStart}<br>
        <strong>Project:</strong> ${project || '(none)'}<br>
        <strong>Tasks (${tasks.length}):</strong><br>` +
        tasks.slice(0, 20).map(t => `&nbsp;&nbsp;• ${t.title}`).join('<br>') +
        (tasks.length > 20 ? `<br>&nbsp;&nbsp;… and ${tasks.length - 20} more` : '');

    document.getElementById('import-error').classList.add('hidden');
    document.getElementById('import-confirm-btn').classList.remove('hidden');
    document.getElementById('import-modal').classList.remove('hidden');
}

function showImportError(msg) {
    document.getElementById('import-error').textContent = msg;
    document.getElementById('import-error').classList.remove('hidden');
    document.getElementById('import-modal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('import-modal').classList.add('hidden');
    document.getElementById('import-preview').classList.add('hidden');
    document.getElementById('import-error').classList.add('hidden');
    document.getElementById('import-confirm-btn').classList.add('hidden');
    importParsed = null;
}

async function confirmImport() {
    if (!importParsed) return;
    const btn = document.getElementById('import-confirm-btn');
    btn.disabled = true; btn.textContent = 'Importing…';
    const resp = await fetch(appUrl + '/weekly-plan/import-excel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(importParsed),
    });
    const json = await resp.json();
    if (json.success) { window.location.href = json.redirect; }
    else { showImportError(json.error || 'Import failed.'); btn.disabled = false; btn.textContent = 'Import Plan'; }
}

function exportExcel() {
    const cards = document.querySelectorAll('#plans-grid [data-project]');
    const rows  = [['Week Start','Project','Status','Assigned To','Progress %','Summary']];
    cards.forEach(c => {
        rows.push([c.dataset.week, c.dataset.project, c.dataset.status,
                   c.dataset.assigned, c.dataset.progress,
                   c.querySelector('.line-clamp-2')?.textContent.trim() || '']);
    });
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(rows), 'Weekly Plans');
    XLSX.writeFile(wb, 'weekly_plans_' + new Date().toISOString().slice(0,10) + '.xlsx');
}

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc   = new jsPDF({ orientation: 'landscape' });
    const cards = document.querySelectorAll('#plans-grid [data-project]');
    doc.setFontSize(16); doc.text('Weekly Plans', 14, 15);
    doc.setFontSize(10); doc.text('Generated: ' + new Date().toLocaleDateString(), 14, 22);
    const body = [];
    cards.forEach(c => {
        body.push([c.dataset.week, c.dataset.project,
                   c.dataset.status.replace('_',' '), c.dataset.assigned || 'Unassigned',
                   c.dataset.progress + '%',
                   c.querySelector('.line-clamp-2')?.textContent.trim() || '']);
    });
    doc.autoTable({ head:[['Week Start','Project','Status','Assigned To','Progress %','Summary']], body,
                    startY: 28, theme:'grid', headStyles:{ fillColor:[37,99,235] } });
    doc.save('weekly_plans_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>
</body>
</html>
