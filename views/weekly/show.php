<?php
$pageTitle = 'Plan Semanal — Semana del ' . date('d/m/Y', strtotime($plan['week_start'] ?? 'now'));
$statusColors = [
    'pending'     => 'bg-slate-700 text-slate-300 border-slate-600',
    'in_progress' => 'bg-yellow-900/50 text-yellow-300 border-yellow-700',
    'completed'   => 'bg-green-900/50 text-green-300 border-green-700',
];
$planStatusLabels = [
    'pending'     => 'Pendiente',
    'in_progress' => 'En progreso',
    'completed'   => 'Completado',
];

// Find badge color from projects list
$badgeColor = '#3B82F6';
foreach ($projects as $p) {
    if ($p['name'] === $plan['project']) {
        $badgeColor = $p['color'];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | HelpDesk</title>
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

            <div class="max-w-3xl mx-auto">
                <div class="mb-6 flex items-center justify-between">
                    <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan" class="text-slate-400 hover:text-white text-sm transition-colors">← Back to Plans</a>
                    <div class="flex items-center gap-3">
                        <button onclick="exportPlanExcel()" class="flex items-center gap-1.5 text-green-400 hover:text-green-300 text-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            Excel
                        </button>
                        <button onclick="exportPlanPDF()" class="flex items-center gap-1.5 text-red-400 hover:text-red-300 text-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            PDF
                        </button>
                        <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                        <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>/copy-next-week">
                            <button type="submit" class="flex items-center gap-1.5 text-purple-400 hover:text-purple-300 text-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                </svg>
                                Copy to Next Week
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if ($user['role'] === 'admin'): ?>
                        <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>/delete"
                              onsubmit="return confirm('Delete this weekly plan?')">
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition-colors">Delete</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Plan Header -->
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 mb-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-sm font-bold text-white px-3 py-1 rounded-lg"
                                      style="background-color: <?= htmlspecialchars($badgeColor) ?>">
                                    <?= htmlspecialchars($plan['project']) ?>
                                </span>
                                <span class="text-xs px-3 py-1 rounded border <?= $statusColors[$plan['status']] ?? '' ?>">
                                    <?= $planStatusLabels[$plan['status']] ?? htmlspecialchars(str_replace('_', ' ', ucfirst($plan['status']))) ?>
                                </span>
                            </div>
                            <h2 class="text-xl font-bold text-white">Semana del <?= htmlspecialchars(date('d/m/Y', strtotime($plan['week_start']))) ?></h2>
                            <p class="text-slate-400 text-sm mt-1">
                                Asignado a: <strong class="text-slate-300"><?= htmlspecialchars($plan['assigned_name'] ?? 'Sin asignar') ?></strong>
                                · Creado por: <?= htmlspecialchars($plan['creator_name'] ?? 'Desconocido') ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-white"><?= htmlspecialchars((string)$plan['progress_percent']) ?>%</p>
                            <p class="text-xs text-slate-500">completado</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="w-full bg-slate-700 rounded-full h-3">
                            <div class="bg-blue-500 h-3 rounded-full transition-all" style="width: <?= min(100, (int)$plan['progress_percent']) ?>%"></div>
                        </div>
                    </div>

                    <?php if (!empty($plan['summary'])): ?>
                    <div class="mt-4 bg-slate-900/50 rounded-lg p-4 text-slate-300 text-sm whitespace-pre-wrap">
                        <?= htmlspecialchars($plan['summary']) ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($plan['file_path'])): ?>
                    <div class="mt-4">
                        <a href="<?= htmlspecialchars($appUrl) ?>/uploads/<?= htmlspecialchars($plan['file_path']) ?>"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download attached plan file
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

<?php
$taskStatusColors = [
    'pending'     => 'bg-slate-700 text-slate-300 border-slate-600',
    'in_progress' => 'bg-yellow-900/50 text-yellow-300 border-yellow-700',
    'done'        => 'bg-green-900/50 text-green-300 border-green-700',
];
$taskStatusLabels = [
    'pending'     => 'Pendiente',
    'in_progress' => 'En progreso',
    'done'        => 'Hecho',
];
?>
                <!-- Tasks -->
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        Tareas
                        <span class="text-slate-400 text-sm font-normal">
                            (<?= count(array_filter($plan['tasks'] ?? [], fn($t) => $t['status'] === 'done')) ?>/<?= count($plan['tasks'] ?? []) ?> hechas)
                        </span>
                    </h3>

                    <?php if (empty($plan['tasks'])): ?>
                    <p class="text-slate-500 text-sm">No hay tareas aún.</p>
                    <?php else: ?>
                    <div class="space-y-2 mb-4" id="task-list">
                        <?php foreach ($plan['tasks'] as $task): ?>
                        <div class="bg-slate-900/50 rounded-lg px-4 py-3">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="text-sm flex-1 min-w-0 <?= $task['status'] === 'done' ? 'line-through text-slate-500' : 'text-slate-200' ?>">
                                    <?= htmlspecialchars($task['title']) ?>
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded border <?= $taskStatusColors[$task['status']] ?? 'bg-slate-700 text-slate-300 border-slate-600' ?>">
                                    <?= $taskStatusLabels[$task['status']] ?? htmlspecialchars($task['status']) ?>
                                </span>
                                <?php if (!empty($task['assigned_name'])): ?>
                                <span class="text-xs text-slate-400 whitespace-nowrap">
                                    👤 <?= htmlspecialchars($task['assigned_name']) ?>
                                </span>
                                <?php else: ?>
                                <span class="text-xs text-slate-600 whitespace-nowrap">General</span>
                                <?php endif; ?>
                                <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                                <button type="button"
                                        onclick="openTaskEdit(<?= (int)$task['id'] ?>, <?= (int)$plan['id'] ?>, '<?= htmlspecialchars($task['status']) ?>', <?= $task['assigned_to'] ? (int)$task['assigned_to'] : 'null' ?>)"
                                        class="text-xs text-slate-500 hover:text-blue-400 transition-colors ml-auto flex-shrink-0">
                                    Editar
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>/task" class="mt-4 space-y-2">
                        <div class="flex gap-2 flex-wrap">
                            <input type="text" name="title" required placeholder="Nueva tarea..."
                                   class="flex-1 min-w-0 bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm">
                            <select name="assigned_to" class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">General</option>
                                <?php foreach ($allUsers as $u): ?>
                                <option value="<?= htmlspecialchars((string)$u['id']) ?>"><?= htmlspecialchars($u['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">Agregar</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>

                <!-- Task Edit Modal -->
                <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                <div id="task-edit-modal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 w-full max-w-sm mx-4">
                        <h3 class="text-lg font-semibold text-white mb-4">Actualizar tarea</h3>
                        <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/task/update-status">
                            <input type="hidden" name="task_id" id="modal-task-id">
                            <input type="hidden" name="plan_id" value="<?= htmlspecialchars((string)$plan['id']) ?>">
                            <div class="mb-4">
                                <label class="block text-sm text-slate-300 mb-1">Estado</label>
                                <select name="status" id="modal-status" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 text-sm">
                                    <option value="pending">Pendiente</option>
                                    <option value="in_progress">En progreso</option>
                                    <option value="done">Hecho</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="block text-sm text-slate-300 mb-1">Asignado a</label>
                                <select name="assigned_to" id="modal-assigned" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 text-sm">
                                    <option value="">General</option>
                                    <?php foreach ($allUsers as $u): ?>
                                    <option value="<?= htmlspecialchars((string)$u['id']) ?>"><?= htmlspecialchars($u['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg transition-colors">Guardar</button>
                                <button type="button" onclick="closeTaskEdit()" class="text-slate-400 hover:text-white text-sm px-4 py-2 transition-colors">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Update Status -->
                <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Actualizar estado del plan</h3>
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>/status" class="flex gap-3 flex-wrap">
                        <?php foreach (['pending', 'in_progress', 'completed'] as $s): ?>
                        <button type="submit" name="status" value="<?= $s ?>"
                                class="px-4 py-2 rounded-lg text-sm border transition-colors
                                       <?= $plan['status'] === $s
                                           ? 'bg-blue-600 text-white border-blue-500'
                                           : 'border-slate-600 text-slate-400 hover:border-blue-500 hover:text-white' ?>">
                            <?= $planStatusLabels[$s] ?>
                        </button>
                        <?php endforeach; ?>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
const planData = <?= json_encode([
    'id'           => $plan['id'],
    'week_start'   => $plan['week_start'],
    'project'      => $plan['project'],
    'status'       => $plan['status'],
    'progress'     => $plan['progress_percent'],
    'assigned'     => $plan['assigned_name'] ?? 'Unassigned',
    'summary'      => $plan['summary'] ?? '',
    'tasks'        => array_map(fn($t) => ['title' => $t['title'], 'status' => $t['status']], $plan['tasks'] ?? []),
]) ?>;

function openTaskEdit(taskId, planId, status, assignedTo) {
    document.getElementById('modal-task-id').value = taskId;
    document.getElementById('modal-status').value = status;
    const assignedSelect = document.getElementById('modal-assigned');
    if (assignedSelect) {
        assignedSelect.value = assignedTo !== null ? assignedTo : '';
    }
    document.getElementById('task-edit-modal').classList.remove('hidden');
}

function closeTaskEdit() {
    document.getElementById('task-edit-modal').classList.add('hidden');
}

function exportPlanExcel() {
    const rows = [['Week Start', 'Project', 'Status', 'Assigned To', 'Progress %', 'Summary']];
    rows.push([planData.week_start, planData.project, planData.status, planData.assigned, planData.progress, planData.summary]);
    rows.push([]);
    rows.push(['Task', 'Status']);
    planData.tasks.forEach(t => rows.push([t.title, t.status]));

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(rows), 'Weekly Plan');
    XLSX.writeFile(wb, 'plan_' + planData.week_start + '.xlsx');
}

function exportPlanPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.setFontSize(16);
    doc.text('Weekly Plan – ' + planData.week_start, 14, 15);
    doc.setFontSize(11);
    doc.text('Project: ' + planData.project, 14, 24);
    doc.text('Status: ' + planData.status.replace('_', ' '), 14, 31);
    doc.text('Assigned to: ' + planData.assigned, 14, 38);
    doc.text('Progress: ' + planData.progress + '%', 14, 45);
    if (planData.summary) {
        doc.setFontSize(10);
        const lines = doc.splitTextToSize(planData.summary, 180);
        doc.text(lines, 14, 54);
    }

    const taskBody = planData.tasks.map(t => [t.title, t.status === 'done' ? '✓ Done' : 'Pending']);
    doc.autoTable({ head:[['Task', 'Status']], body: taskBody, startY: 64, theme:'grid',
                    headStyles:{ fillColor:[37,99,235] } });
    doc.save('plan_' + planData.week_start + '.pdf');
}
</script>
</body>
</html>
