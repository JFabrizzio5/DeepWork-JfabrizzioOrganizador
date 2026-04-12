<?php $pageTitle = 'Weekly Dashboard'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Dashboard | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
    <!-- SheetJS for Excel export -->
    <script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
    <!-- jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white">Weekly Dashboard</h2>
                    <p class="text-slate-400 text-sm mt-1">History of weekly plans, task completion, and progress over time.</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="exportDashboardExcel()" class="flex items-center gap-2 bg-green-700 hover:bg-green-600 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        Export Excel
                    </button>
                    <button onclick="exportDashboardPDF()" class="flex items-center gap-2 bg-red-700 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Export PDF
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <?php if (!empty($summaries)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
                <?php
                $latest = $summaries[0] ?? null;
                $prev   = $summaries[1] ?? null;
                ?>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                    <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Current Week Plans</p>
                    <p class="text-3xl font-bold text-white"><?= htmlspecialchars((string)($latest['total_plans'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-500 mt-1"><?= $latest ? htmlspecialchars(date('M j, Y', strtotime($latest['week_start']))) : '–' ?></p>
                </div>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                    <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Avg Progress (Latest Week)</p>
                    <p class="text-3xl font-bold text-white"><?= htmlspecialchars((string)($latest['avg_progress'] ?? 0)) ?>%</p>
                    <p class="text-xs text-slate-500 mt-1"><?= $prev ? 'Last: ' . htmlspecialchars((string)($prev['avg_progress'] ?? 0)) . '%' : '' ?></p>
                </div>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                    <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Completed (Latest Week)</p>
                    <p class="text-3xl font-bold text-green-400"><?= htmlspecialchars((string)($latest['completed'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-500 mt-1"><?= $prev ? 'Last week: ' . htmlspecialchars((string)($prev['completed'] ?? 0)) : '' ?></p>
                </div>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                    <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">In Progress (Latest Week)</p>
                    <p class="text-3xl font-bold text-yellow-400"><?= htmlspecialchars((string)($latest['in_progress'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-500 mt-1">Pending: <?= htmlspecialchars((string)($latest['pending'] ?? 0)) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- History Table (summaries per week) -->
            <?php if (!empty($summaries)): ?>
            <div class="bg-slate-800 rounded-xl border border-slate-700 mb-8 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-700">
                    <h3 class="text-lg font-semibold text-white">Weekly History</h3>
                    <p class="text-slate-400 text-xs mt-0.5">Last 12 weeks at a glance.</p>
                </div>
                <div class="overflow-x-auto">
                    <table id="history-table" class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-700 text-slate-400 text-xs uppercase tracking-wider">
                                <th class="px-5 py-3 text-left">Week Start</th>
                                <th class="px-5 py-3 text-center">Total Plans</th>
                                <th class="px-5 py-3 text-center">Completed</th>
                                <th class="px-5 py-3 text-center">In Progress</th>
                                <th class="px-5 py-3 text-center">Pending</th>
                                <th class="px-5 py-3 text-center">Avg Progress</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php foreach ($summaries as $s): ?>
                            <tr class="hover:bg-slate-700/40 transition-colors">
                                <td class="px-5 py-3 font-medium text-white"><?= htmlspecialchars(date('M j, Y', strtotime($s['week_start']))) ?></td>
                                <td class="px-5 py-3 text-center text-slate-300"><?= htmlspecialchars((string)$s['total_plans']) ?></td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs bg-green-900/50 text-green-300"><?= htmlspecialchars((string)$s['completed']) ?></span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs bg-yellow-900/50 text-yellow-300"><?= htmlspecialchars((string)$s['in_progress']) ?></span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs bg-slate-700 text-slate-300"><?= htmlspecialchars((string)$s['pending']) ?></span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-20 bg-slate-700 rounded-full h-1.5">
                                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: <?= min(100, (int)$s['avg_progress']) ?>%"></div>
                                        </div>
                                        <span class="text-slate-300 text-xs"><?= htmlspecialchars((string)$s['avg_progress']) ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Plans by Week (expanded view) -->
            <?php if (!empty($byWeek)): ?>
            <div class="space-y-6">
                <?php foreach ($byWeek as $weekStart => $weekPlans): ?>
                <div class="bg-slate-800 rounded-xl border border-slate-700">
                    <div class="px-5 py-4 border-b border-slate-700 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-white">
                            Week of <?= htmlspecialchars(date('F j, Y', strtotime($weekStart))) ?>
                        </h3>
                        <span class="text-xs text-slate-400"><?= count($weekPlans) ?> plan(s)</span>
                    </div>
                    <div class="divide-y divide-slate-700">
                        <?php foreach ($weekPlans as $plan): ?>
                        <?php
                            $doneTasks  = count(array_filter($plan['tasks'] ?? [], fn($t) => $t['status'] === 'done'));
                            $totalTasks = count($plan['tasks'] ?? []);
                            $statusColors = [
                                'pending'     => 'bg-slate-700 text-slate-300',
                                'in_progress' => 'bg-yellow-900/50 text-yellow-300',
                                'completed'   => 'bg-green-900/50 text-green-300',
                            ];
                        ?>
                        <div class="px-5 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded text-white flex-shrink-0"
                                          style="background-color: #3B82F6">
                                        <?= htmlspecialchars($plan['project']) ?>
                                    </span>
                                    <span class="text-sm text-white truncate">
                                        <?= htmlspecialchars($plan['assigned_name'] ?? 'Unassigned') ?>
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded <?= $statusColors[$plan['status']] ?? 'bg-slate-700 text-slate-300' ?>">
                                        <?= htmlspecialchars(str_replace('_', ' ', ucfirst($plan['status']))) ?>
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 flex-shrink-0">
                                    <span class="text-xs text-slate-400"><?= $doneTasks ?>/<?= $totalTasks ?> tasks</span>
                                    <div class="w-24 bg-slate-700 rounded-full h-1.5">
                                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: <?= min(100, (int)$plan['progress_percent']) ?>%"></div>
                                    </div>
                                    <span class="text-xs text-slate-300 w-8 text-right"><?= htmlspecialchars((string)$plan['progress_percent']) ?>%</span>
                                    <a href="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>"
                                       class="text-blue-400 hover:text-blue-300 text-xs transition-colors">View</a>
                                    <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/weekly-plan/<?= htmlspecialchars((string)$plan['id']) ?>/copy-next-week">
                                        <button type="submit" title="Copy all tasks to next week"
                                                class="text-purple-400 hover:text-purple-300 text-xs transition-colors whitespace-nowrap">
                                            → Next Week
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($plan['tasks'])): ?>
                            <ul class="mt-3 space-y-1 pl-2">
                                <?php foreach ($plan['tasks'] as $task): ?>
                                <li class="flex items-center gap-2 text-xs">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0 <?= $task['status'] === 'done' ? 'bg-green-500' : 'bg-slate-600' ?>"></span>
                                    <span class="<?= $task['status'] === 'done' ? 'line-through text-slate-500' : 'text-slate-300' ?>">
                                        <?= htmlspecialchars($task['title']) ?>
                                    </span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-16 text-slate-500">
                <p class="text-lg font-medium">No recent weekly plans found.</p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
// Data passed from PHP for export
const summaryData = <?= json_encode($summaries) ?>;
const weekData    = <?= json_encode($byWeek) ?>;

function exportDashboardExcel() {
    const wb = XLSX.utils.book_new();

    // Sheet 1: Weekly Summary
    const summaryRows = [['Week Start', 'Total Plans', 'Completed', 'In Progress', 'Pending', 'Avg Progress %']];
    summaryData.forEach(s => {
        summaryRows.push([s.week_start, s.total_plans, s.completed, s.in_progress, s.pending, s.avg_progress]);
    });
    const wsSummary = XLSX.utils.aoa_to_sheet(summaryRows);
    XLSX.utils.book_append_sheet(wb, wsSummary, 'Weekly Summary');

    // Sheet 2: Plans Detail
    const detailRows = [['Week Start', 'Project', 'Assigned To', 'Status', 'Progress %', 'Task', 'Task Status']];
    Object.entries(weekData).forEach(([weekStart, plans]) => {
        plans.forEach(plan => {
            if (plan.tasks && plan.tasks.length > 0) {
                plan.tasks.forEach(task => {
                    detailRows.push([weekStart, plan.project, plan.assigned_name || 'Unassigned', plan.status, plan.progress_percent, task.title, task.status]);
                });
            } else {
                detailRows.push([weekStart, plan.project, plan.assigned_name || 'Unassigned', plan.status, plan.progress_percent, '', '']);
            }
        });
    });
    const wsDetail = XLSX.utils.aoa_to_sheet(detailRows);
    XLSX.utils.book_append_sheet(wb, wsDetail, 'Plans Detail');

    XLSX.writeFile(wb, 'weekly_dashboard_' + new Date().toISOString().slice(0,10) + '.xlsx');
}

function exportDashboardPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape' });

    doc.setFontSize(16);
    doc.text('Weekly Dashboard Report', 14, 15);
    doc.setFontSize(10);
    doc.text('Generated: ' + new Date().toLocaleDateString(), 14, 22);

    const summaryHead = [['Week Start', 'Total Plans', 'Completed', 'In Progress', 'Pending', 'Avg Progress %']];
    const summaryBody = summaryData.map(s => [s.week_start, s.total_plans, s.completed, s.in_progress, s.pending, s.avg_progress + '%']);

    doc.autoTable({ head: summaryHead, body: summaryBody, startY: 28, theme: 'grid', headStyles: { fillColor: [37, 99, 235] } });

    let yPos = doc.lastAutoTable.finalY + 12;
    doc.setFontSize(13);
    doc.text('Plans Detail', 14, yPos);
    yPos += 6;

    const detailHead = [['Week Start', 'Project', 'Assigned To', 'Status', 'Progress', 'Task', 'Task Status']];
    const detailBody = [];
    Object.entries(weekData).forEach(([weekStart, plans]) => {
        plans.forEach(plan => {
            if (plan.tasks && plan.tasks.length > 0) {
                plan.tasks.forEach(task => {
                    detailBody.push([weekStart, plan.project, plan.assigned_name || 'Unassigned', plan.status, plan.progress_percent + '%', task.title, task.status]);
                });
            } else {
                detailBody.push([weekStart, plan.project, plan.assigned_name || 'Unassigned', plan.status, plan.progress_percent + '%', '–', '–']);
            }
        });
    });

    doc.autoTable({ head: detailHead, body: detailBody, startY: yPos, theme: 'grid', headStyles: { fillColor: [37, 99, 235] } });
    doc.save('weekly_dashboard_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>
</body>
</html>
