<?php
$pageTitle = 'Ticket #' . ($ticket['id'] ?? '');
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
    'support' => 'Soporte',
    'bug'     => 'Error',
    'feature' => 'Mejora',
    'query'   => 'Consulta',
];
$impactLabels = [
    'low'      => 'Bajo',
    'medium'   => 'Medio',
    'high'     => 'Alto',
    'critical' => 'Crítico',
];
$impactColors = [
    'low'      => 'bg-green-900/40 text-green-300',
    'medium'   => 'bg-yellow-900/40 text-yellow-300',
    'high'     => 'bg-orange-900/40 text-orange-300',
    'critical' => 'bg-red-900/40 text-red-300',
];
$phaseLabels = [
    'information' => 'Información',
    'creation'    => 'Creación',
    'in_progress' => 'En progreso',
    'review'      => 'Revisión',
    'done'        => 'Finalizado',
];
$isVip    = !empty($ticket['requester_is_vip']);
$vipColor = $ticket['requester_highlight_color'] ?? '#F59E0B';
?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="max-w-5xl mx-auto">
                <a href="<?= htmlspecialchars($appUrl) ?>/tickets/list"
                   class="inline-flex items-center gap-1 text-slate-400 hover:text-white text-sm mb-4 transition-colors">
                    ← Volver a tickets
                </a>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Contenido principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Encabezado del ticket -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6"
                             <?php if ($isVip): ?>style="border-left: 4px solid <?= htmlspecialchars($vipColor) ?>; box-shadow: 0 0 16px <?= htmlspecialchars($vipColor) ?>30;"<?php endif; ?>>
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-slate-400 text-sm font-mono">#<?= htmlspecialchars((string)$ticket['id']) ?></p>
                                        <?php if ($isVip): ?>
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background:<?= htmlspecialchars($vipColor) ?>20; color:<?= htmlspecialchars($vipColor) ?>; border:1px solid <?= htmlspecialchars($vipColor) ?>60;">⭐ VIP</span>
                                        <?php endif; ?>
                                        <?php if (!empty($ticket['is_resolved'])): ?>
                                        <span class="text-xs bg-green-900/50 text-green-300 border border-green-700 px-2 py-0.5 rounded-full">✓ Resuelto</span>
                                        <?php endif; ?>
                                    </div>
                                    <h2 class="text-xl font-bold text-white mt-1">
                                        <?= htmlspecialchars($ticket['title'] ?: 'Ticket #' . $ticket['id']) ?>
                                    </h2>
                                </div>
                                <span class="text-xs px-3 py-1 rounded-full border whitespace-nowrap <?= $statusColors[$ticket['status']] ?? 'bg-slate-700 text-slate-300 border-slate-600' ?>">
                                    <?= htmlspecialchars($statusLabels[$ticket['status']] ?? ucfirst(str_replace('_', ' ', $ticket['status']))) ?>
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded">
                                    <?= htmlspecialchars($typeLabels[$ticket['type']] ?? ucfirst($ticket['type'])) ?>
                                </span>
                                <span class="text-xs px-2 py-1 rounded <?= $impactColors[$ticket['impact']] ?? 'bg-slate-700 text-slate-300' ?>">
                                    Impacto: <?= htmlspecialchars($impactLabels[$ticket['impact']] ?? ucfirst($ticket['impact'])) ?>
                                </span>
                                <span class="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded">
                                    Fase: <?= htmlspecialchars($phaseLabels[$ticket['phase']] ?? ucfirst(str_replace('_', ' ', $ticket['phase']))) ?>
                                </span>
                                <?php if (!empty($ticket['sucursal_nombre'])): ?>
                                <span class="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded">
                                    🏢 <?= htmlspecialchars($ticket['sucursal_nombre']) ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($ticket['escalation'] === 'escalate'): ?>
                                <span class="text-xs bg-red-900/50 text-red-300 border border-red-700 px-2 py-1 rounded">🔴 Escalar</span>
                                <?php elseif ($ticket['escalation'] === 'no_escalate'): ?>
                                <span class="text-xs bg-blue-900/50 text-blue-300 border border-blue-700 px-2 py-1 rounded">🔵 No escalar</span>
                                <?php endif; ?>
                            </div>

                            <div class="prose-sm text-slate-300 whitespace-pre-wrap bg-slate-900/50 rounded-lg p-4 text-sm leading-relaxed">
                                <?= htmlspecialchars($ticket['description']) ?>
                            </div>

                            <?php if (!empty($ticket['steps_to_reproduce'])): ?>
                            <div class="mt-4">
                                <h4 class="text-sm font-semibold text-slate-400 mb-2">Pasos del problema</h4>
                                <div class="bg-slate-900/50 rounded-lg p-4 text-sm text-slate-300 whitespace-pre-wrap"><?= htmlspecialchars($ticket['steps_to_reproduce']) ?></div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($ticket['technical_context'])): ?>
                            <div class="mt-4">
                                <h4 class="text-sm font-semibold text-slate-400 mb-2">Contexto técnico</h4>
                                <div class="bg-slate-900/50 rounded-lg p-4 text-sm text-slate-300 whitespace-pre-wrap"><?= htmlspecialchars($ticket['technical_context']) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Notas -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                Notas <span class="text-slate-400 text-sm font-normal">(<?= count($notes) ?>)</span>
                            </h3>

                            <?php if (empty($notes)): ?>
                            <p class="text-slate-500 text-sm">Sin notas aún.</p>
                            <?php else: ?>
                            <div class="space-y-4 mb-6">
                                <?php foreach ($notes as $note): ?>
                                <div class="bg-slate-900/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-blue-400"><?= htmlspecialchars($note['user_name']) ?></span>
                                        <span class="text-xs text-slate-500"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($note['created_at']))) ?></span>
                                    </div>
                                    <p class="text-sm text-slate-300 whitespace-pre-wrap"><?= htmlspecialchars($note['note']) ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/note" class="mt-4">
                                <label class="block text-sm font-medium text-slate-300 mb-2">Agregar nota interna</label>
                                <textarea name="note" rows="3" required placeholder="Escribe una nota interna..."
                                          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none text-sm"></textarea>
                                <button type="submit" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">Agregar nota</button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <!-- Evidencias -->
                        <?php if (!empty($evidences)): ?>
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                Evidencias <span class="text-slate-400 text-sm font-normal">(<?= count($evidences) ?>)</span>
                            </h3>
                            <div class="space-y-2">
                                <?php foreach ($evidences as $ev): ?>
                                <div class="flex items-center justify-between bg-slate-900/50 rounded-lg px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs bg-slate-700 text-slate-400 px-2 py-0.5 rounded uppercase"><?= htmlspecialchars($ev['file_type']) ?></span>
                                        <span class="text-sm text-slate-300"><?= htmlspecialchars($ev['original_name']) ?></span>
                                        <span class="text-xs text-slate-500"><?= htmlspecialchars(number_format($ev['file_size'] / 1024, 1)) ?> KB</span>
                                    </div>
                                    <a href="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/evidence/<?= htmlspecialchars((string)$ev['id']) ?>"
                                       class="text-blue-400 hover:text-blue-300 text-sm">Descargar</a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Panel lateral -->
                    <div class="space-y-6">
                        <!-- Detalles -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Detalles</h3>
                            <dl class="space-y-3 text-sm">
                                <div>
                                    <dt class="text-slate-500">Solicitante</dt>
                                    <dd class="text-slate-200 mt-0.5 flex items-center gap-1">
                                        <?php if ($isVip): ?>
                                        <span style="color:<?= htmlspecialchars($vipColor) ?>">⭐</span>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($ticket['requester_name'] ?: 'N/A') ?>
                                    </dd>
                                    <dd class="text-slate-400 text-xs"><?= htmlspecialchars($ticket['requester_email']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Asignado a</dt>
                                    <dd class="text-slate-200 mt-0.5"><?= htmlspecialchars($ticket['assigned_name'] ?? 'Sin asignar') ?></dd>
                                </div>
                                <?php if (!empty($ticket['sucursal_nombre'])): ?>
                                <div>
                                    <dt class="text-slate-500">Sucursal</dt>
                                    <dd class="text-slate-200 mt-0.5">🏢 <?= htmlspecialchars($ticket['sucursal_nombre']) ?></dd>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <dt class="text-slate-500">Creado</dt>
                                    <dd class="text-slate-200 mt-0.5"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ticket['created_at']))) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Actualizado</dt>
                                    <dd class="text-slate-200 mt-0.5"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ticket['updated_at']))) ?></dd>
                                </div>
                            </dl>
                        </div>

                        <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                        <!-- Panel de escalación -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Escalación</h3>
                            <div class="space-y-2">
                                <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/escalation">
                                    <input type="hidden" name="escalation" value="escalate">
                                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors <?= $ticket['escalation'] === 'escalate' ? 'bg-red-900/60 text-red-200 border border-red-700' : 'bg-slate-700 text-slate-300 hover:bg-red-900/40 hover:text-red-300' ?>">
                                        🔴 Marcar para Escalar
                                    </button>
                                </form>
                                <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/escalation">
                                    <input type="hidden" name="escalation" value="no_escalate">
                                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors <?= $ticket['escalation'] === 'no_escalate' ? 'bg-blue-900/60 text-blue-200 border border-blue-700' : 'bg-slate-700 text-slate-300 hover:bg-blue-900/40 hover:text-blue-300' ?>">
                                        🔵 Marcar No Escalar
                                    </button>
                                </form>
                                <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/escalation">
                                    <input type="hidden" name="escalation" value="none">
                                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-slate-300 transition-colors">
                                        ✕ Limpiar
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Toggle resuelto -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Resolución</h3>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/toggle-resolved">
                                <?php if (!empty($ticket['is_resolved'])): ?>
                                <button type="submit" class="w-full bg-green-900/60 text-green-200 border border-green-700 text-sm py-2 rounded-lg hover:bg-slate-700 hover:text-slate-300 hover:border-slate-600 transition-colors">
                                    ✓ Resuelto — Marcar como No Resuelto
                                </button>
                                <?php else: ?>
                                <button type="submit" class="w-full bg-slate-700 text-slate-300 text-sm py-2 rounded-lg hover:bg-green-900/40 hover:text-green-300 transition-colors">
                                    ✓ Marcar como Resuelto
                                </button>
                                <?php endif; ?>
                            </form>
                        </div>

                        <!-- Actualizar estado -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Actualizar Estado</h3>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/status" class="space-y-3">
                                <div>
                                    <label class="block text-xs text-slate-400 mb-1">Estado</label>
                                    <select name="status" class="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        <?php foreach (['new' => 'Nuevo', 'in_progress' => 'En progreso', 'review' => 'En revisión', 'done' => 'Finalizado'] as $val => $lbl): ?>
                                        <option value="<?= $val ?>" <?= $ticket['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-400 mb-1">Fase</label>
                                    <select name="phase" class="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        <?php foreach (['information' => 'Información', 'creation' => 'Creación', 'in_progress' => 'En progreso', 'review' => 'Revisión', 'done' => 'Finalizado'] as $val => $lbl): ?>
                                        <option value="<?= $val ?>" <?= $ticket['phase'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 rounded-lg transition-colors">Actualizar</button>
                            </form>
                        </div>
                        <?php endif; ?>

                        <!-- Asignar (solo admin) -->
                        <?php if ($user['role'] === 'admin'): ?>
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Asignar Desarrollador</h3>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/assign" class="space-y-3">
                                <select name="assigned_to" class="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                    <option value="">Seleccionar desarrollador...</option>
                                    <?php foreach ($developers as $dev): ?>
                                    <option value="<?= htmlspecialchars((string)$dev['id']) ?>" <?= (int)($ticket['assigned_to'] ?? 0) === (int)$dev['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dev['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="w-full bg-green-700 hover:bg-green-600 text-white text-sm py-2 rounded-lg transition-colors">Asignar</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
