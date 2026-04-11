<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 text-slate-100 flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">HelpDesk</h1>
            <p class="text-slate-400 mt-1">CometaxCompany · @jfabrizzio</p>
        </div>

        <div class="bg-slate-800 rounded-2xl p-8 border border-slate-700 shadow-xl">
            <h2 class="text-xl font-semibold text-white mb-6">Sign in to your account</h2>

            <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 bg-red-900/50 border border-red-700 rounded-lg text-red-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= htmlspecialchars($appUrl) ?>/login" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Email address</label>
                    <input type="email" id="email" name="email" required
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           placeholder="admin@helpdesk.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           placeholder="••••••••">
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors mt-2">
                    Sign In
                </button>
            </form>

            <p class="text-center text-slate-500 text-xs mt-6">
                Default: admin@helpdesk.com / password
            </p>
        </div>
    </div>
</body>
</html>
