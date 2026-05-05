<?php $pageTitle = htmlspecialchars($project['name']); ?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <!-- Header -->
            <div class="flex items-center gap-4 mb-6">
                <a href="<?= htmlspecialchars($appUrl) ?>/projects" class="text-slate-400 hover:text-white text-sm">← Proyectos</a>
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-5 h-5 rounded-full flex-shrink-0"
                          style="background-color: <?= htmlspecialchars($project['color']) ?>"></span>
                    <h2 class="text-2xl font-bold text-white truncate"><?= htmlspecialchars($project['name']) ?></h2>
                </div>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="<?= htmlspecialchars($appUrl) ?>/admin/projects/<?= htmlspecialchars((string)$project['id']) ?>/members"
                   class="ml-auto text-xs bg-slate-700 hover:bg-slate-600 text-slate-300 px-3 py-1.5 rounded-lg transition-colors">
                    👥 Ver miembros
                </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($project['description'])): ?>
            <p class="text-slate-400 text-sm mb-6 -mt-2"><?= htmlspecialchars($project['description']) ?></p>
            <?php endif; ?>

            <!-- Tabs -->
            <div x-data="{ tab: 'files' }">
                <div class="flex gap-1 mb-6 bg-slate-800 rounded-lg p-1 w-fit border border-slate-700">
                    <button @click="tab='files'"
                            :class="tab==='files' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        📁 Recursos
                    </button>
                    <button @click="tab='notes'"
                            :class="tab==='notes' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        💬 Comunicación
                    </button>
                    <button @click="tab='profile'"
                            :class="tab==='profile' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        👤 Mi Perfil
                    </button>
                </div>

                <!-- ── RECURSOS (Files) ───────────────────────────── -->
                <div x-show="tab==='files'">
                    <?php $canUpload = in_array($user['role'] ?? '', ['admin', 'dev']); ?>
                    <?php if ($canUpload): ?>
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-5 mb-5">
                        <h3 class="text-sm font-semibold text-slate-300 mb-3">Subir recurso</h3>
                        <form method="POST"
                              action="<?= htmlspecialchars($appUrl) ?>/projects/<?= htmlspecialchars((string)$project['id']) ?>/file"
                              enctype="multipart/form-data"
                              class="flex flex-wrap gap-3 items-end">
                            <div class="flex-1 min-w-48">
                                <label class="block text-xs text-slate-400 mb-1">Archivo <span class="text-red-400">*</span></label>
                                <input type="file" name="project_file" required
                                       class="w-full text-sm text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                            </div>
                            <div class="flex-1 min-w-48">
                                <label class="block text-xs text-slate-400 mb-1">Descripción</label>
                                <input type="text" name="description" maxlength="255"
                                       placeholder="Descripción opcional..."
                                       class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-1.5 text-white text-sm placeholder-slate-400 focus:outline-none focus:border-blue-500">
                            </div>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                                Subir
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                        <div class="px-5 py-3 border-b border-slate-700">
                            <h3 class="text-sm font-semibold text-white">Archivos del proyecto <span class="text-slate-400 font-normal">(<?= count($files) ?>)</span></h3>
                        </div>
                        <?php if (empty($files)): ?>
                        <div class="p-8 text-center text-slate-500 text-sm">No hay archivos subidos aún.</div>
                        <?php else: ?>
                        <ul class="divide-y divide-slate-700">
                            <?php foreach ($files as $f): ?>
                            <li class="flex items-center gap-4 px-5 py-3 hover:bg-slate-700/30 transition-colors">
                                <div class="w-8 h-8 bg-slate-700 rounded flex items-center justify-center text-xs font-bold text-slate-300 uppercase flex-shrink-0">
                                    <?= htmlspecialchars(strtoupper(pathinfo($f['original_name'], PATHINFO_EXTENSION))) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($f['original_name']) ?></p>
                                    <?php if (!empty($f['description'])): ?>
                                    <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars($f['description']) ?></p>
                                    <?php endif; ?>
                                    <p class="text-xs text-slate-500">
                                        <?= htmlspecialchars($f['uploader_name']) ?> · <?= htmlspecialchars(date('d/m/Y H:i', strtotime($f['created_at']))) ?>
                                        · <?= number_format(($f['file_size'] ?? 0) / 1024, 1) ?> KB
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="<?= htmlspecialchars($appUrl) ?>/projects/<?= htmlspecialchars((string)$project['id']) ?>/file/<?= htmlspecialchars((string)$f['id']) ?>"
                                       class="text-blue-400 hover:text-blue-300 text-xs transition-colors">Descargar</a>
                                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                                    <form method="POST"
                                          action="<?= htmlspecialchars($appUrl) ?>/projects/<?= htmlspecialchars((string)$project['id']) ?>/file/<?= htmlspecialchars((string)$f['id']) ?>/delete"
                                          onsubmit="return confirm('¿Eliminar este archivo?')">
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">Eliminar</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ── COMUNICACIÓN (Notes) ───────────────────────── -->
                <div x-show="tab==='notes'">
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-5 mb-5">
                        <form method="POST"
                              action="<?= htmlspecialchars($appUrl) ?>/projects/<?= htmlspecialchars((string)$project['id']) ?>/note"
                              class="flex gap-3 items-end">
                            <div class="flex-1">
                                <label class="block text-xs text-slate-400 mb-1">Nuevo mensaje</label>
                                <textarea name="note" rows="2" required
                                          placeholder="Escribe un mensaje para el equipo..."
                                          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"></textarea>
                            </div>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                                Enviar
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        <?php if (empty($notes)): ?>
                        <div class="text-center py-8 text-slate-500 text-sm">No hay mensajes aún. ¡Sé el primero en escribir!</div>
                        <?php else: ?>
                        <?php foreach ($notes as $n): ?>
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-blue-700 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                <?= htmlspecialchars(strtoupper(substr($n['author_name'], 0, 1))) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-white"><?= htmlspecialchars($n['author_name']) ?></span>
                                    <span class="text-xs text-slate-500"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($n['created_at']))) ?></span>
                                </div>
                                <p class="text-sm text-slate-300 whitespace-pre-wrap"><?= htmlspecialchars($n['note']) ?></p>
                            </div>
                            <?php if (($user['role'] ?? '') === 'admin'): ?>
                            <form method="POST"
                                  action="<?= htmlspecialchars($appUrl) ?>/projects/<?= htmlspecialchars((string)$project['id']) ?>/note/<?= htmlspecialchars((string)$n['id']) ?>/delete"
                                  onsubmit="return confirm('¿Eliminar este mensaje?')">
                                <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">×</button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ── MI PERFIL ──────────────────────────────────── -->
                <div x-show="tab==='profile'">
                    <div class="max-w-xl">
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                            <h3 class="text-base font-semibold text-white mb-4">Mi perfil en «<?= htmlspecialchars($project['name']) ?>»</h3>
                            <form method="POST"
                                  action="<?= htmlspecialchars($appUrl) ?>/projects/<?= htmlspecialchars((string)$project['id']) ?>/profile"
                                  class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Nombre visible en el proyecto</label>
                                    <input type="text" name="display_name" maxlength="100"
                                           value="<?= htmlspecialchars($profile['display_name'] ?? '') ?>"
                                           placeholder="<?= htmlspecialchars($user['name']) ?>"
                                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Rol / Bio en el proyecto</label>
                                    <textarea name="bio" rows="3"
                                              class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none"
                                              placeholder="Tu rol, responsabilidades o descripción dentro del proyecto..."><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Contacto (Slack, teléfono, etc.)</label>
                                    <input type="text" name="contact_info" maxlength="255"
                                           value="<?= htmlspecialchars($profile['contact_info'] ?? '') ?>"
                                           placeholder="@slack, +52 55 1234 5678..."
                                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500">
                                </div>
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                                    Guardar perfil
                                </button>
                            </form>
                        </div>

                        <!-- Team members quick view -->
                        <?php if (!empty($members)): ?>
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5 mt-5">
                            <h3 class="text-sm font-semibold text-slate-300 mb-3">Miembros del proyecto</h3>
                            <ul class="space-y-2">
                                <?php foreach ($members as $m): ?>
                                <li class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-full bg-slate-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                        <?= htmlspecialchars(strtoupper(substr($m['name'], 0, 1))) ?>
                                    </div>
                                    <div>
                                        <span class="text-sm text-white"><?= htmlspecialchars($m['name']) ?></span>
                                        <span class="text-xs text-slate-400 ml-1"><?= htmlspecialchars($m['role']) ?></span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div><!-- /x-data -->
        </main>
    </div>
</div>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
