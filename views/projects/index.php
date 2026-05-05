<?php $pageTitle = 'Mis Proyectos'; ?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Proyectos | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
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
                    <h2 class="text-2xl font-bold text-white">Mis Proyectos</h2>
                    <p class="text-slate-400 text-sm mt-1">Proyectos a los que tienes acceso.</p>
                </div>
            </div>

            <?php if (empty($projects)): ?>
            <div class="text-center py-16 text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                <p class="text-lg font-medium">No tienes proyectos asignados</p>
                <p class="text-sm mt-1">Contacta al administrador para que te asigne a un proyecto.</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($projects as $p): ?>
                <a href="<?= htmlspecialchars($appUrl) ?>/projects/<?= htmlspecialchars((string)$p['id']) ?>"
                   class="bg-slate-800 rounded-xl border border-slate-700 p-5 hover:border-slate-500 transition-colors group block">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-4 h-4 rounded-full flex-shrink-0"
                              style="background-color: <?= htmlspecialchars($p['color']) ?>"></span>
                        <h3 class="text-lg font-semibold text-white group-hover:text-blue-400 transition-colors truncate">
                            <?= htmlspecialchars($p['name']) ?>
                        </h3>
                    </div>
                    <?php if (!empty($p['description'])): ?>
                    <p class="text-slate-400 text-sm line-clamp-2"><?= htmlspecialchars($p['description']) ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-slate-500 mt-3">Ver detalle →</p>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
