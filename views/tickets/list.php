<?php
$pageTitle = 'Tickets';
$statusLabels = [
    'new'         => 'Nuevo',
    'in_progress' => 'En progreso',
    'review'      => 'En revisión',
    'done'        => 'Finalizado',
];
$statusColors = [
    'new'         => 'bg-blue-900/50 text-blue-300 border-blue-700',
    'in_progress' => 'bg-yellow-900/50 text-yellow-300 border-yellow-700',
    'review'      => 'bg-purple-900/50 text-purple-300 border-purple-700',
    'done'        => 'bg-green-900/50 text-green-300 border-green-700',
];
$typeLabels = [
    'support'       => 'Soporte',
    'bug'           => 'Error',
    'feature'       => 'Mejora',
    'query'         => 'Consulta',
    'requerimiento' => 'Requerimiento',
    'cambio'        => 'Cambio',
];
$impactLabels = [
    'low'      => 'Bajo',
    'medium'   => 'Medio',
    'high'     => 'Alto',
    'critical' => 'Crítico',
];
$impactColors = [
    'low'      => 'text-green-400',
    'medium'   => 'text-yellow-400',
    'high'     => 'text-orange-400',
    'critical' => 'text-red-400',
];
?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <?php if (($user['role'] ?? 'user') !== 'user'): ?>
            <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 mb-6">
                <form method="GET" action="<?= htmlspecialchars($appUrl) ?>/tickets/list">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-3">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Estado</label>
                            <select name="status" class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">Todos</option>
                                <?php foreach ($statusLabels as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($filters['status'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Tipo</label>
                            <select name="type" class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">Todos</option>
                                <?php foreach ($typeLabels as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($filters['type'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Impacto</label>
                            <select name="impact" class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">Todos</option>
                                <?php foreach ($impactLabels as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($filters['impact'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Escalación</label>
                            <select name="escalation" class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">Todos</option>
                                <option value="escalate" <?= ($filters['escalation'] ?? '') === 'escalate' ? 'selected' : '' ?>>🔴 Escalar</option>
                                <option value="no_escalate" <?= ($filters['escalation'] ?? '') === 'no_escalate' ? 'selected' : '' ?>>🔵 No escalar</option>
                                <option value="none" <?= ($filters['escalation'] ?? '') === 'none' ? 'selected' : '' ?>>Sin marcar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Resolución</label>
                            <select name="is_resolved" class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">Todos</option>
                                <option value="1" <?= ($filters['is_resolved'] ?? '') === '1' ? 'selected' : '' ?>>✅ Resueltos</option>
                                <option value="0" <?= ($filters['is_resolved'] ?? '') === '0' ? 'selected' : '' ?>>⏳ Sin resolver</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Sucursal</label>
                            <select name="sucursal_id" class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">Todas</option>
                                <?php foreach ($sucursales as $suc): ?>
                                <option value="<?= htmlspecialchars((string)$suc['id']) ?>" <?= ($filters['sucursal_id'] ?? '') == $suc['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($suc['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Proyecto</label>
                            <select name="project_id" class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">Todos</option>
                                <?php foreach ($projects ?? [] as $proj): ?>
                                <option value="<?= htmlspecialchars((string)$proj['id']) ?>" <?= ($filters['project_id'] ?? '') == $proj['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($proj['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Fecha desde</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>"
                                   class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Fecha hasta</label>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>"
                                   class="w-full bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="highlighted" value="1" <?= !empty($filters['highlighted']) ? 'checked' : '' ?>
                                       class="w-4 h-4 rounded bg-slate-700 border-slate-600 text-yellow-500 focus:ring-yellow-500">
                                <span class="text-xs text-slate-300">⭐ Solo VIP</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">Filtrar</button>
                        <a href="<?= htmlspecialchars($appUrl) ?>/tickets/list" class="text-slate-400 hover:text-white text-sm px-3 py-2">Limpiar</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Tabla de tickets -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">
                        <?= (($user['role'] ?? 'user') === 'user') ? 'Mis Solicitudes' : 'Todos los Tickets' ?> <span class="text-slate-400 text-sm font-normal">(<?= count($tickets) ?>)</span>
                    </h2>
                    <a href="<?= htmlspecialchars($appUrl) ?>/tickets/create" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">+ Nuevo Ticket</a>
                </div>

                <?php if (empty($tickets)): ?>
                <div class="p-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-lg font-medium">No se encontraron tickets</p>
                    <p class="text-sm mt-1">Crea tu primer ticket para comenzar</p>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-700/50 text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">#ID</th>
                                <th class="px-4 py-3 text-left">Título / Descripción</th>
                                <th class="px-4 py-3 text-left">Tipo</th>
                                <th class="px-4 py-3 text-left">Impacto</th>
                                <th class="px-4 py-3 text-left">Estado</th>
                                <th class="px-4 py-3 text-left">Escalación</th>
                                <th class="px-4 py-3 text-left">Sucursal</th>
                                <th class="px-4 py-3 text-left">Asignado</th>
                                <th class="px-4 py-3 text-left">Creado</th>
                                <th class="px-4 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php foreach ($tickets as $ticket): ?>
                            <?php
                                $isVip    = !empty($ticket['requester_is_vip']);
                                $vipColor = $ticket['requester_highlight_color'] ?? '#F59E0B';
                                if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $vipColor)) { $vipColor = '#F59E0B'; }
                                $rowBorderStyle = $isVip ? "border-left: 4px solid {$vipColor};" : '';
                                $isResolved = !empty($ticket['is_resolved']);
                            ?>
                            <tr class="hover:bg-slate-700/30 transition-colors <?= $isResolved ? 'opacity-60' : '' ?>"
                                style="<?= htmlspecialchars($rowBorderStyle) ?>">
                                <td class="px-4 py-3 text-slate-400 font-mono">
                                    #<?= htmlspecialchars((string)$ticket['id']) ?>
                                    <?php if ($isVip): ?>
                                    <span class="ml-1" title="Usuario VIP" style="color:<?= htmlspecialchars($vipColor) ?>">⭐</span>
                                    <?php endif; ?>
                                    <?php if ($isResolved): ?>
                                    <span class="ml-1 text-green-400" title="Resuelto">✓</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-white truncate max-w-xs">
                                        <?= htmlspecialchars($ticket['title'] ?: substr($ticket['description'], 0, 60) . '...') ?>
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($ticket['requester_email']) ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded">
                                        <?= htmlspecialchars($typeLabels[$ticket['type']] ?? ucfirst($ticket['type'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-medium <?= $impactColors[$ticket['impact']] ?? 'text-slate-400' ?>">
                                        <?= htmlspecialchars($impactLabels[$ticket['impact']] ?? ucfirst($ticket['impact'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-1 rounded border <?= $statusColors[$ticket['status']] ?? 'bg-slate-700 text-slate-300 border-slate-600' ?>">
                                        <?= htmlspecialchars($statusLabels[$ticket['status']] ?? ucfirst(str_replace('_', ' ', $ticket['status']))) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($ticket['escalation'] === 'escalate'): ?>
                                        <span class="text-xs bg-red-900/50 text-red-300 border border-red-700 px-2 py-1 rounded">🔴 Escalar</span>
                                    <?php elseif ($ticket['escalation'] === 'no_escalate'): ?>
                                        <span class="text-xs bg-blue-900/50 text-blue-300 border border-blue-700 px-2 py-1 rounded">🔵 No escalar</span>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-600">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-xs">
                                    <?= htmlspecialchars($ticket['sucursal_nombre'] ?? '—') ?>
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-xs">
                                    <?= htmlspecialchars($ticket['assigned_name'] ?? 'Sin asignar') ?>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs">
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($ticket['created_at']))) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>"
                                       class="text-blue-400 hover:text-blue-300 text-xs font-medium">Ver →</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>
