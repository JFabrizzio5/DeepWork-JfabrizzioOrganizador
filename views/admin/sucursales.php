<?php $pageTitle = 'Sucursales'; ?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="max-w-3xl mx-auto space-y-6">
                <!-- Formulario nueva sucursal -->
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">Agregar Sucursal</h2>
                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/sucursales/store" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Nombre <span class="text-red-400">*</span></label>
                            <input type="text" name="nombre" required maxlength="100"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                   placeholder="Nombre de la sucursal">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Descripción</label>
                            <textarea name="descripcion" rows="2"
                                      class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"
                                      placeholder="Descripción opcional"></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg transition-colors">
                            Crear Sucursal
                        </button>
                    </form>
                </div>

                <!-- Lista de sucursales -->
                <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-700">
                        <h2 class="text-lg font-semibold text-white">
                            Sucursales <span class="text-slate-400 text-sm font-normal">(<?= count($sucursales) ?>)</span>
                        </h2>
                    </div>

                    <?php if (empty($sucursales)): ?>
                    <div class="p-12 text-center text-slate-500">
                        <p class="text-lg font-medium">No hay sucursales registradas</p>
                        <p class="text-sm mt-1">Agrega la primera sucursal con el formulario de arriba</p>
                    </div>
                    <?php else: ?>
                    <div class="divide-y divide-slate-700">
                        <?php foreach ($sucursales as $suc): ?>
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-white">
                                    🏢 <?= htmlspecialchars($suc['nombre']) ?>
                                </p>
                                <?php if (!empty($suc['descripcion'])): ?>
                                <p class="text-sm text-slate-400 mt-0.5"><?= htmlspecialchars($suc['descripcion']) ?></p>
                                <?php endif; ?>
                                <p class="text-xs text-slate-500 mt-0.5">Creada: <?= htmlspecialchars(date('d/m/Y', strtotime($suc['created_at']))) ?></p>
                            </div>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/sucursales/<?= htmlspecialchars((string)$suc['id']) ?>/delete"
                                  onsubmit="return confirm('¿Eliminar la sucursal «<?= htmlspecialchars(addslashes($suc['nombre'])) ?>»?')">
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition-colors">Eliminar</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
