<?php $pageTitle = 'Miembros – ' . htmlspecialchars($project['name']); ?>
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

            <div class="flex items-center gap-3 mb-6">
                <a href="<?= htmlspecialchars($appUrl) ?>/admin/projects" class="text-slate-400 hover:text-white text-sm">← Proyectos</a>
                <span class="text-slate-600">/</span>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full inline-block"
                          style="background-color: <?= htmlspecialchars($project['color']) ?>"></span>
                    <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($project['name']) ?> — Miembros</h2>
                </div>
            </div>

            <?php if (empty($members)): ?>
            <div class="text-center py-16 text-slate-500">
                <p class="text-lg font-medium">No hay miembros asignados a este proyecto.</p>
                <p class="text-sm mt-1">Asigna usuarios desde la gestión de usuarios.</p>
                <a href="<?= htmlspecialchars($appUrl) ?>/admin/users"
                   class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                    Ir a Usuarios
                </a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($members as $m): ?>
                <?php $profile = $profileMap[(int)$m['id']] ?? []; ?>
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                            <?= htmlspecialchars(strtoupper(substr($m['name'], 0, 1))) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="text-white font-semibold truncate">
                                <?= htmlspecialchars(!empty($profile['display_name']) ? $profile['display_name'] : $m['name']) ?>
                            </p>
                            <?php if (!empty($profile['display_name']) && $profile['display_name'] !== $m['name']): ?>
                            <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($m['name']) ?></p>
                            <?php endif; ?>
                            <span class="text-xs px-1.5 py-0.5 rounded bg-slate-700 text-slate-300">
                                <?= htmlspecialchars($m['role']) ?>
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400 truncate mb-1">✉ <?= htmlspecialchars($m['email']) ?></p>

                    <?php if (!empty($profile['bio'])): ?>
                    <p class="text-sm text-slate-300 mt-2 line-clamp-3"><?= htmlspecialchars($profile['bio']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($profile['contact_info'])): ?>
                    <p class="text-xs text-slate-400 mt-2">📞 <?= htmlspecialchars($profile['contact_info']) ?></p>
                    <?php endif; ?>

                    <!-- Admin can edit any member's profile -->
                    <form method="POST"
                          action="<?= htmlspecialchars($appUrl) ?>/projects/<?= htmlspecialchars((string)$project['id']) ?>/profile"
                          class="mt-4 space-y-2 border-t border-slate-700 pt-4">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars((string)$m['id']) ?>">
                        <div>
                            <label class="block text-xs text-slate-400 mb-0.5">Nombre visible</label>
                            <input type="text" name="display_name" maxlength="100"
                                   value="<?= htmlspecialchars($profile['display_name'] ?? '') ?>"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-1.5 text-white text-xs focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-0.5">Bio / Rol</label>
                            <textarea name="bio" rows="2"
                                      class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-1.5 text-white text-xs focus:outline-none focus:border-blue-500 resize-none"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-0.5">Contacto</label>
                            <input type="text" name="contact_info" maxlength="255"
                                   value="<?= htmlspecialchars($profile['contact_info'] ?? '') ?>"
                                   class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-1.5 text-white text-xs focus:outline-none focus:border-blue-500">
                        </div>
                        <button type="submit"
                                class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition-colors">
                            Guardar perfil
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
