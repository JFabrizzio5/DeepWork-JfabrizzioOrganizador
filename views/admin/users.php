<?php $pageTitle = 'Usuarios'; ?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">
                        Gestión de Usuarios <span class="text-slate-400 text-sm font-normal">(<?= count($users) ?>)</span>
                    </h2>
                    <a href="<?= htmlspecialchars($appUrl) ?>/admin/users/create"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        + Agregar Usuario
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-700/50 text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Nombre</th>
                                <th class="px-4 py-3 text-left">Correo</th>
                                <th class="px-4 py-3 text-left">Rol</th>
                                <th class="px-4 py-3 text-left">VIP / Destacado</th>
                                <th class="px-4 py-3 text-left">Sucursales</th>
                                <th class="px-4 py-3 text-left">Creado</th>
                                <th class="px-4 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php foreach ($users as $u): ?>
                            <?php
                                $isVip    = !empty($u['is_vip']);
                                $vipColor = $u['highlight_color'] ?? '#F59E0B';
                            ?>
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars((string)$u['id']) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white"
                                             style="background-color: <?= $isVip ? htmlspecialchars($vipColor) : '#1d4ed8' ?>;">
                                            <?= htmlspecialchars(strtoupper(substr($u['name'], 0, 1))) ?>
                                        </div>
                                        <div>
                                            <span class="text-white font-medium"><?= htmlspecialchars($u['name']) ?></span>
                                            <?php if ($isVip): ?>
                                            <span class="ml-1 text-xs" style="color:<?= htmlspecialchars($vipColor) ?>">⭐</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-400"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="px-4 py-3">
                                    <?php if ((int)$u['id'] !== $user['id']): ?>
                                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/users/<?= htmlspecialchars((string)$u['id']) ?>/role"
                                          class="flex items-center gap-1">
                                        <select name="role"
                                                onchange="this.form.submit()"
                                                class="text-xs px-2 py-1 rounded border bg-slate-800 border-slate-600 text-slate-200 cursor-pointer focus:outline-none focus:border-blue-500">
                                            <option value="user"  <?= $u['role'] === 'user'  ? 'selected' : '' ?>>Usuario</option>
                                            <option value="dev"   <?= $u['role'] === 'dev'   ? 'selected' : '' ?>>Dev</option>
                                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </form>
                                    <?php else: ?>
                                    <?php
                                    $roleBadge = match($u['role']) {
                                        'admin' => 'bg-red-900/50 text-red-300 border-red-700',
                                        'dev'   => 'bg-blue-900/50 text-blue-300 border-blue-700',
                                        default => 'bg-slate-700 text-slate-300 border-slate-600',
                                    };
                                    $roleLabel = match($u['role']) {
                                        'admin' => 'Admin',
                                        'dev'   => 'Dev',
                                        default => 'Usuario',
                                    };
                                    ?>
                                    <span class="text-xs px-2 py-1 rounded border <?= $roleBadge ?>"><?= $roleLabel ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/users/<?= htmlspecialchars((string)$u['id']) ?>/highlight"
                                          class="flex items-center gap-2">
                                        <input type="hidden" name="is_vip" id="vip_flag_<?= $u['id'] ?>" value="<?= (int)$isVip ?>">
                                        <input type="hidden" name="highlight_color" id="vip_color_<?= $u['id'] ?>" value="<?= htmlspecialchars($vipColor) ?>">
                                        <button type="button"
                                                title="<?= $isVip ? 'Quitar VIP' : 'Marcar VIP' ?>"
                                                onclick="toggleVip(<?= json_encode((int)$u['id']) ?>, <?= (int)$isVip ?>)"
                                                class="text-xl transition-colors <?= $isVip ? '' : 'opacity-30 hover:opacity-80' ?>"
                                                style="<?= $isVip ? "color:{$vipColor};" : 'color:#F59E0B;' ?>">★</button>
                                        <input type="color"
                                               value="<?= htmlspecialchars($vipColor) ?>"
                                               title="Color de destacado"
                                               onchange="document.getElementById('vip_color_<?= $u['id'] ?>').value=this.value; this.form.submit();"
                                               class="w-7 h-7 rounded cursor-pointer bg-transparent border-0 p-0">
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                    // Build a map of sucursal_ids for this user
                                    $userSucursalIds = [];
                                    // We can't easily get per-user sucursales here without extra query
                                    // Show assign form inline
                                    ?>
                                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/users/<?= htmlspecialchars((string)$u['id']) ?>/sucursales" class="flex flex-wrap gap-1 items-center">
                                        <?php
                                        $assignedIds = $userSucursalMap[(int)$u['id']] ?? [];
                                        foreach ($sucursales as $suc):
                                        ?>
                                        <label class="flex items-center gap-1 text-xs text-slate-300 cursor-pointer">
                                            <input type="checkbox" name="sucursal_ids[]" value="<?= htmlspecialchars((string)$suc['id']) ?>"
                                                   <?= in_array((string)$suc['id'], array_map('strval', $assignedIds)) ? 'checked' : '' ?>
                                                   class="w-3 h-3 rounded bg-slate-700 border-slate-600 text-blue-500">
                                            <?= htmlspecialchars($suc['nombre']) ?>
                                        </label>
                                        <?php endforeach; ?>
                                        <button type="submit" class="text-xs text-blue-400 hover:text-blue-300 ml-1">Guardar</button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs"><?= htmlspecialchars(date('d/m/Y', strtotime($u['created_at']))) ?></td>
                                <td class="px-4 py-3">
                                    <?php if ((int)$u['id'] !== $user['id']): ?>
                                    <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/admin/users/<?= htmlspecialchars((string)$u['id']) ?>/delete"
                                          onsubmit="return confirm('¿Eliminar al usuario <?= htmlspecialchars(addslashes($u['name'])) ?>?')">
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">Eliminar</button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-slate-600 text-xs">Tú</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
function toggleVip(userId, current) {
    const newVal = current ? 0 : 1;
    document.getElementById('vip_flag_' + userId).value = newVal;
    document.getElementById('vip_flag_' + userId).closest('form').submit();
}
</script>
</body>
</html>
