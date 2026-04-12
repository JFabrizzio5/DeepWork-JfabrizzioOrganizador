<?php $pageTitle = 'Nuevo Ticket'; ?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ticket | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="max-w-3xl mx-auto">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white">Nuevo Reporte de Incidencia</h2>
                    <p class="text-slate-400 mt-1">Completa el formulario paso a paso para registrar tu incidencia.</p>
                </div>

                <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/store" enctype="multipart/form-data" class="space-y-6">

                    <!-- Paso 1: Información básica -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">1</span>
                            <h3 class="text-lg font-semibold text-white">Información básica</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Título <span class="text-slate-500">(opcional)</span></label>
                                <input type="text" name="title" maxlength="200"
                                       class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                       placeholder="Resumen breve del problema">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Descripción <span class="text-red-400">*</span></label>
                                <textarea name="description" rows="4" required
                                          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"
                                          placeholder="Describe brevemente el problema"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Pasos del problema -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">2</span>
                            <h3 class="text-lg font-semibold text-white">¿Qué pasó paso a paso?</h3>
                        </div>
                        <p class="text-xs text-slate-400 mb-3">Agrega los pasos que llevaron al problema. Puedes agregar tantos como necesites.</p>
                        <div id="steps-container" class="space-y-2 mb-3">
                            <div class="flex items-center gap-2 step-row">
                                <span class="text-slate-400 text-sm w-5 text-right step-num">1.</span>
                                <input type="text" name="step[]" placeholder="Paso 1..."
                                       class="flex-1 bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm">
                                <button type="button" onclick="removeStep(this)" class="text-slate-500 hover:text-red-400 text-lg leading-none">×</button>
                            </div>
                        </div>
                        <button type="button" onclick="addStep()"
                                class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Agregar paso
                        </button>
                        <input type="hidden" name="steps_to_reproduce" id="steps_to_reproduce_hidden">
                        <div class="mt-4 pt-4 border-t border-slate-700">
                            <label class="block text-sm font-medium text-slate-300 mb-1">Contexto técnico</label>
                            <textarea name="technical_context" rows="2"
                                      class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none text-sm"
                                      placeholder="Entorno, versión, sistema operativo, navegador..."></textarea>
                        </div>
                    </div>

                    <!-- Paso 3: Clasificación -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">3</span>
                            <h3 class="text-lg font-semibold text-white">Clasificación</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Tipo</label>
                                <select name="type" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                    <option value="support">Soporte</option>
                                    <option value="bug">Error</option>
                                    <option value="feature">Mejora</option>
                                    <option value="query">Consulta</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Impacto</label>
                                <select name="impact" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                    <option value="low">Bajo</option>
                                    <option value="medium" selected>Medio</option>
                                    <option value="high">Alto</option>
                                    <option value="critical">Crítico</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Prioridad</label>
                                <select name="priority_user" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                    <option value="low">Baja</option>
                                    <option value="medium" selected>Media</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 4: Sucursal -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">4</span>
                            <h3 class="text-lg font-semibold text-white">Sucursal</h3>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Sucursal <span class="text-slate-500">(opcional)</span></label>
                            <select name="sucursal_id" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500">
                                <option value="">Sin sucursal</option>
                                <?php foreach ($sucursales as $suc): ?>
                                <option value="<?= htmlspecialchars((string)$suc['id']) ?>">
                                    <?= htmlspecialchars($suc['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Paso 5: Información del solicitante -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">5</span>
                            <h3 class="text-lg font-semibold text-white">Información del solicitante</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Nombre</label>
                                <input type="text" name="requester_name" value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                       class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                       placeholder="Tu nombre">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Correo electrónico <span class="text-red-400">*</span></label>
                                <input type="email" name="requester_email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                       class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500"
                                       placeholder="correo@ejemplo.com">
                            </div>
                        </div>
                    </div>

                    <!-- Paso 6: Evidencias -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">6</span>
                            <h3 class="text-lg font-semibold text-white">Evidencias / Adjuntos</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Permitido: PNG, JPG, PDF, XML, ZIP, MP4. Se pueden adjuntar varios archivos.</p>
                        <input type="file" name="evidence[]" multiple accept=".png,.jpg,.jpeg,.pdf,.xml,.zip,.mp4"
                               class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                            Enviar Reporte
                        </button>
                        <a href="<?= htmlspecialchars($appUrl) ?>/tickets/list" class="text-slate-400 hover:text-white transition-colors">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
let stepCount = 1;

function addStep() {
    stepCount++;
    const container = document.getElementById('steps-container');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 step-row';
    row.innerHTML = `
        <span class="text-slate-400 text-sm w-5 text-right step-num">${stepCount}.</span>
        <input type="text" name="step[]" placeholder="Paso ${stepCount}..."
               class="flex-1 bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm">
        <button type="button" onclick="removeStep(this)" class="text-slate-500 hover:text-red-400 text-lg leading-none">×</button>
    `;
    container.appendChild(row);
    renumberSteps();
}

function removeStep(btn) {
    const rows = document.querySelectorAll('.step-row');
    if (rows.length <= 1) return;
    btn.closest('.step-row').remove();
    renumberSteps();
}

function renumberSteps() {
    const rows = document.querySelectorAll('.step-row');
    rows.forEach((row, i) => {
        const num = row.querySelector('.step-num');
        const input = row.querySelector('input');
        if (num) num.textContent = (i + 1) + '.';
        if (input) input.placeholder = 'Paso ' + (i + 1) + '...';
    });
    stepCount = rows.length;
}

function buildSteps() {
    const inputs = document.querySelectorAll('#steps-container input[name="step[]"]');
    const steps = [];
    inputs.forEach((input, i) => {
        const val = input.value.trim();
        if (val) steps.push((i + 1) + '. ' + val);
    });
    document.getElementById('steps_to_reproduce_hidden').value = steps.join('\n');
}

document.querySelector('form').addEventListener('submit', function() {
    buildSteps();
});
</script>
</body>
</html>
