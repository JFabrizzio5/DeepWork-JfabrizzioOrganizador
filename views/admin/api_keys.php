<?php
// Admin-only page: manage API keys
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Keys | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100">
<div class="flex h-screen overflow-hidden">
    <?php include dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <div class="flex flex-col flex-1 overflow-hidden">
        <?php include dirname(__DIR__) . '/partials/header.php'; ?>
        <main class="flex-1 overflow-y-auto p-6">
            <?php include dirname(__DIR__) . '/partials/flash.php'; ?>

            <!-- Generate new key form -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">🔑 Generate New API Key</h2>
                <form action="<?= htmlspecialchars($appUrl) ?>/admin/api-keys/generate" method="POST" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="name" placeholder="Key label (e.g. CI Pipeline, Mobile App)"
                           required
                           class="flex-1 bg-slate-700 border border-slate-600 text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
                    <select name="user_id"
                            class="bg-slate-700 border border-slate-600 text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
                        <?php foreach ($users as $u): ?>
                        <option value="<?= htmlspecialchars((string)$u['id']) ?>">
                            <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['role']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg transition-colors font-medium">
                        Generate Key
                    </button>
                </form>
                <?php if (!empty($newToken)): ?>
                <div class="mt-4 p-4 bg-green-900/40 border border-green-600 rounded-lg">
                    <p class="text-green-300 text-sm font-medium mb-2">✅ New API key generated. Copy it now — it will not be shown again:</p>
                    <div class="flex items-center gap-3">
                        <code id="newToken" class="flex-1 bg-slate-900 text-green-300 text-sm px-4 py-2 rounded font-mono break-all">
                            <?= htmlspecialchars($newToken) ?>
                        </code>
                        <button onclick="copyToken()" class="bg-slate-700 hover:bg-slate-600 text-white text-xs px-3 py-2 rounded transition-colors">
                            Copy
                        </button>
                    </div>
                </div>
                <script>
                    function copyToken() {
                        const t = document.getElementById('newToken').innerText.trim();
                        navigator.clipboard.writeText(t).then(() => alert('Copied to clipboard!'));
                    }
                </script>
                <?php endif; ?>
            </div>

            <!-- API Keys table -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">
                        All API Keys <span class="text-slate-400 text-sm font-normal">(<?= count($apiKeys) ?>)</span>
                    </h2>
                </div>

                <?php if (empty($apiKeys)): ?>
                <div class="text-center py-16 text-slate-500">
                    <p class="text-4xl mb-3">🔑</p>
                    <p class="text-lg">No API keys yet. Generate one above.</p>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-700/50 text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Label</th>
                                <th class="px-4 py-3 text-left">Owner</th>
                                <th class="px-4 py-3 text-left">Role</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Last Used</th>
                                <th class="px-4 py-3 text-left">Created</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php foreach ($apiKeys as $k): ?>
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars((string)$k['id']) ?></td>
                                <td class="px-4 py-3 text-white font-medium"><?= htmlspecialchars($k['name']) ?></td>
                                <td class="px-4 py-3 text-slate-300">
                                    <?= htmlspecialchars($k['user_name']) ?><br>
                                    <span class="text-xs text-slate-500"><?= htmlspecialchars($k['user_email']) ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                    $rb = match($k['user_role']) {
                                        'admin' => 'bg-red-900/50 text-red-300 border-red-700',
                                        'dev'   => 'bg-blue-900/50 text-blue-300 border-blue-700',
                                        default => 'bg-slate-700 text-slate-300 border-slate-600',
                                    };
                                    ?>
                                    <span class="px-2 py-0.5 rounded text-xs border <?= $rb ?>">
                                        <?= htmlspecialchars($k['user_role']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($k['is_active']): ?>
                                        <span class="px-2 py-0.5 rounded text-xs border bg-green-900/50 text-green-300 border-green-700">active</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-xs border bg-red-900/50 text-red-300 border-red-700">revoked</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-xs">
                                    <?= $k['last_used_at'] ? htmlspecialchars($k['last_used_at']) : '—' ?>
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-xs">
                                    <?= htmlspecialchars($k['created_at']) ?>
                                </td>
                                <td class="px-4 py-3 flex items-center gap-2">
                                    <?php if ($k['is_active']): ?>
                                    <form action="<?= htmlspecialchars($appUrl) ?>/admin/api-keys/<?= htmlspecialchars((string)$k['id']) ?>/revoke" method="POST"
                                          onsubmit="return confirm('Revoke this key?')">
                                        <button type="submit"
                                                class="text-xs bg-yellow-700/30 hover:bg-yellow-700/60 text-yellow-300 border border-yellow-700 px-3 py-1 rounded transition-colors">
                                            Revoke
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <form action="<?= htmlspecialchars($appUrl) ?>/admin/api-keys/<?= htmlspecialchars((string)$k['id']) ?>/delete" method="POST"
                                          onsubmit="return confirm('Delete this key permanently?')">
                                        <button type="submit"
                                                class="text-xs bg-red-900/30 hover:bg-red-700/60 text-red-300 border border-red-700 px-3 py-1 rounded transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick API Reference -->
            <div class="mt-6 bg-slate-800 rounded-xl border border-slate-700 p-6">
                <h3 class="text-base font-semibold text-white mb-3">📡 Quick API Reference</h3>
                <p class="text-slate-400 text-sm mb-4">
                    Include your key in every request via the <code class="text-blue-300">Authorization: Bearer &lt;token&gt;</code> header
                    (or <code class="text-blue-300">?api_key=&lt;token&gt;</code> as a fallback).
                </p>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-slate-300">
                        <thead class="text-slate-500 uppercase">
                            <tr>
                                <th class="text-left pr-4 pb-2">Method</th>
                                <th class="text-left pr-4 pb-2">Endpoint</th>
                                <th class="text-left pb-2">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php
                            $routes = [
                                ['GET',  '/api/tickets',                   'List tickets (role-scoped)'],
                                ['POST', '/api/tickets',                   'Create ticket'],
                                ['GET',  '/api/tickets/{id}',              'Get ticket detail + notes + evidences'],
                                ['POST', '/api/tickets/{id}/status',       'Update status (dev, admin)'],
                                ['POST', '/api/tickets/{id}/phase',        'Update phase (dev, admin)'],
                                ['POST', '/api/tickets/{id}/note',         'Add internal note (dev, admin)'],
                                ['POST', '/api/tickets/{id}/assign',       'Assign to developer (admin)'],
                                ['POST', '/api/tickets/{id}/evidence',     'Upload evidence file (multipart/form-data)'],
                                ['GET',  '/api/tickets/{id}/evidence',     'List evidence files'],
                                ['GET',  '/api/users',                     'List users (admin)'],
                            ];
                            foreach ($routes as [$m, $path, $desc]):
                                $mc = $m === 'GET' ? 'text-green-400' : 'text-yellow-400';
                            ?>
                            <tr>
                                <td class="pr-4 py-1.5 font-mono <?= $mc ?>"><?= $m ?></td>
                                <td class="pr-4 py-1.5 font-mono text-blue-300"><?= htmlspecialchars($path) ?></td>
                                <td class="py-1.5 text-slate-400"><?= htmlspecialchars($desc) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>
