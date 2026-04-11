<?php
$pageTitle = 'Ticket #' . ($ticket['id'] ?? '');
$statusColors = [
    'new'         => 'bg-blue-900/50 text-blue-300 border-blue-700',
    'in_progress' => 'bg-yellow-900/50 text-yellow-300 border-yellow-700',
    'review'      => 'bg-purple-900/50 text-purple-300 border-purple-700',
    'done'        => 'bg-green-900/50 text-green-300 border-green-700',
];
$impactColors = [
    'low'      => 'bg-green-900/40 text-green-300',
    'medium'   => 'bg-yellow-900/40 text-yellow-300',
    'high'     => 'bg-orange-900/40 text-orange-300',
    'critical' => 'bg-red-900/40 text-red-300',
];
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
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
                <!-- Back -->
                <a href="<?= htmlspecialchars($appUrl) ?>/tickets/list" class="inline-flex items-center gap-1 text-slate-400 hover:text-white text-sm mb-4 transition-colors">
                    ← Back to Tickets
                </a>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Ticket Header -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div>
                                    <p class="text-slate-400 text-sm font-mono">#<?= htmlspecialchars((string)$ticket['id']) ?></p>
                                    <h2 class="text-xl font-bold text-white mt-1">
                                        <?= htmlspecialchars($ticket['title'] ?: 'Ticket #' . $ticket['id']) ?>
                                    </h2>
                                </div>
                                <span class="text-xs px-3 py-1 rounded-full border whitespace-nowrap <?= $statusColors[$ticket['status']] ?? 'bg-slate-700 text-slate-300 border-slate-600' ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', ucfirst($ticket['status']))) ?>
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded capitalize"><?= htmlspecialchars($ticket['type']) ?></span>
                                <span class="text-xs px-2 py-1 rounded capitalize <?= $impactColors[$ticket['impact']] ?? 'bg-slate-700 text-slate-300' ?>">
                                    Impact: <?= htmlspecialchars($ticket['impact']) ?>
                                </span>
                                <span class="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded capitalize">
                                    Phase: <?= htmlspecialchars(str_replace('_', ' ', $ticket['phase'])) ?>
                                </span>
                            </div>

                            <div class="prose-sm text-slate-300 whitespace-pre-wrap bg-slate-900/50 rounded-lg p-4 text-sm leading-relaxed">
                                <?= htmlspecialchars($ticket['description']) ?>
                            </div>

                            <?php if (!empty($ticket['steps_to_reproduce'])): ?>
                            <div class="mt-4">
                                <h4 class="text-sm font-semibold text-slate-400 mb-2">Steps to Reproduce</h4>
                                <div class="bg-slate-900/50 rounded-lg p-4 text-sm text-slate-300 whitespace-pre-wrap"><?= htmlspecialchars($ticket['steps_to_reproduce']) ?></div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($ticket['technical_context'])): ?>
                            <div class="mt-4">
                                <h4 class="text-sm font-semibold text-slate-400 mb-2">Technical Context</h4>
                                <div class="bg-slate-900/50 rounded-lg p-4 text-sm text-slate-300 whitespace-pre-wrap"><?= htmlspecialchars($ticket['technical_context']) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Notes -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                Notes <span class="text-slate-400 text-sm font-normal">(<?= count($notes) ?>)</span>
                            </h3>

                            <?php if (empty($notes)): ?>
                            <p class="text-slate-500 text-sm">No notes yet.</p>
                            <?php else: ?>
                            <div class="space-y-4 mb-6">
                                <?php foreach ($notes as $note): ?>
                                <div class="bg-slate-900/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-blue-400"><?= htmlspecialchars($note['user_name']) ?></span>
                                        <span class="text-xs text-slate-500"><?= htmlspecialchars(date('M j, Y H:i', strtotime($note['created_at']))) ?></span>
                                    </div>
                                    <p class="text-sm text-slate-300 whitespace-pre-wrap"><?= htmlspecialchars($note['note']) ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/note" class="mt-4">
                                <label class="block text-sm font-medium text-slate-300 mb-2">Add a note</label>
                                <textarea name="note" rows="3" required placeholder="Write an internal note..."
                                          class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 resize-none text-sm"></textarea>
                                <button type="submit" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">Add Note</button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <!-- Evidences -->
                        <?php if (!empty($evidences)): ?>
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                Evidence <span class="text-slate-400 text-sm font-normal">(<?= count($evidences) ?>)</span>
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
                                       class="text-blue-400 hover:text-blue-300 text-sm">Download</a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Ticket Meta -->
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Details</h3>
                            <dl class="space-y-3 text-sm">
                                <div>
                                    <dt class="text-slate-500">Requester</dt>
                                    <dd class="text-slate-200 mt-0.5"><?= htmlspecialchars($ticket['requester_name'] ?: 'N/A') ?></dd>
                                    <dd class="text-slate-400 text-xs"><?= htmlspecialchars($ticket['requester_email']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Assigned to</dt>
                                    <dd class="text-slate-200 mt-0.5"><?= htmlspecialchars($ticket['assigned_name'] ?? 'Unassigned') ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Created</dt>
                                    <dd class="text-slate-200 mt-0.5"><?= htmlspecialchars(date('M j, Y H:i', strtotime($ticket['created_at']))) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Updated</dt>
                                    <dd class="text-slate-200 mt-0.5"><?= htmlspecialchars(date('M j, Y H:i', strtotime($ticket['updated_at']))) ?></dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Update Status (admin/dev) -->
                        <?php if (in_array($user['role'], ['admin', 'dev'])): ?>
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Update Status</h3>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/status" class="space-y-3">
                                <div>
                                    <label class="block text-xs text-slate-400 mb-1">Status</label>
                                    <select name="status" class="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        <?php foreach (['new', 'in_progress', 'review', 'done'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-400 mb-1">Phase</label>
                                    <select name="phase" class="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        <?php foreach (['information', 'creation', 'in_progress', 'review', 'done'] as $p): ?>
                                        <option value="<?= $p ?>" <?= $ticket['phase'] === $p ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $p)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 rounded-lg transition-colors">Update</button>
                            </form>
                        </div>
                        <?php endif; ?>

                        <!-- Assign (admin only) -->
                        <?php if ($user['role'] === 'admin'): ?>
                        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Assign Developer</h3>
                            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/tickets/<?= htmlspecialchars((string)$ticket['id']) ?>/assign" class="space-y-3">
                                <select name="assigned_to" class="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                    <option value="">Select developer...</option>
                                    <?php foreach ($developers as $dev): ?>
                                    <option value="<?= htmlspecialchars((string)$dev['id']) ?>" <?= (int)($ticket['assigned_to'] ?? 0) === (int)$dev['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dev['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="w-full bg-green-700 hover:bg-green-600 text-white text-sm py-2 rounded-lg transition-colors">Assign</button>
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
