<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | HelpDesk</title>
    <?php include dirname(__DIR__) . '/partials/head.php'; ?>
</head>
<body class="min-h-full bg-slate-900 text-slate-100 flex flex-col items-center justify-center px-4 py-8">

    <!-- Theme toggle (top-right) -->
    <button id="theme-toggle"
            onclick="toggleTheme()"
            title="Cambiar tema"
            class="fixed top-4 right-4 text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-700 transition-colors z-10">
        <svg id="icon-sun" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M18.364 18.364l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <svg id="icon-moon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
    </button>

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">HelpDesk</h1>
            <p class="text-slate-400 mt-1">CometaxCompany · @JFabrizzio5</p>
        </div>

        <div class="bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-700 shadow-xl">
            <h2 class="text-xl font-semibold text-white mb-6">Iniciar sesión</h2>

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
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Correo electrónico</label>
                    <input type="email" id="email" name="email" required
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           placeholder="admin@helpdesk.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           placeholder="••••••••">
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors mt-2">
                    Entrar
                </button>
            </form>
        </div>

        <!-- Social / brand footer -->
        <div class="mt-6 flex items-center justify-center gap-4 flex-wrap">
            <!-- CometaxCompany website -->
            <a href="https://cometax.click/" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-1.5 text-slate-500 hover:text-slate-300 text-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                cometax.click
            </a>
            <!-- Instagram -->
            <a href="https://www.instagram.com/cometaxcompany/" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-1.5 text-slate-500 hover:text-pink-400 text-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>
                </svg>
                @cometaxcompany
            </a>
            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/in/joseph-fabrizzio-hernandez-gonzalez-045b91270/" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-1.5 text-slate-500 hover:text-blue-400 text-xs transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
                LinkedIn
            </a>
            <!-- GitHub -->
            <a href="https://github.com/JFabrizzio5" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-1.5 text-slate-500 hover:text-white text-xs transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                </svg>
                @JFabrizzio5
            </a>
        </div>
    </div>

    <script>
    /* toggleTheme / updateThemeIcons are defined in head.php */
    </script>
</body>
</html>
