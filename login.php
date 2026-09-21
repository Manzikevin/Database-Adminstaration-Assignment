<?php
session_start();
require_once 'config.php';

// Redirect to dashboard if user is already authenticated
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please enter both your email address and password.';
    } else {
        try {
            // Check credentials against users table
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && $password) {
                // Prevent Session Fixation
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = $user['role'] ?? 'Admin';

                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid email address or password.';
            }
        } catch (PDOException $e) {
            $error = 'Authentication service error. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - HOPE SIMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans text-slate-800 min-h-full flex flex-col justify-between antialiased selection:bg-blue-900 selection:text-white bg-gradient-to-br from-slate-100 via-slate-50 to-blue-50/40">

    <!-- Top Decorative Bar -->
    <div class="h-1.5 bg-blue-900 w-full"></div>

    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="w-full max-w-md space-y-8">

            <!-- Brand Identifier Header -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-900 text-white rounded-2xl shadow-lg shadow-blue-900/20 border border-blue-800 mb-4">
                    <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-blue-950 sm:text-3xl">HOPE SIMS</h1>
                <p class="mt-1.5 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    School Information Management System
                </p>
            </div>

            <!-- Login Card Container -->
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-8">
                
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-slate-900">Sign in to your portal</h2>
                    <p class="text-xs text-slate-500 mt-1">Enter your institutional credentials to access your dashboard.</p>
                </div>

                <!-- Error Notification Banner -->
                <?php if (!empty($error)): ?>
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200/80 flex items-start space-x-3 text-red-800">
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-medium leading-relaxed"><?= htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Form Element -->
                <form action="login.php" method="POST" class="space-y-5" x-data="{ showPassword: false }">
                    
                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Institutional Email
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" required autocomplete="email"
                                value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                                placeholder="username@HOPE.ac.rw"
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300/80 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 focus:bg-white transition duration-150">
                        </div>
                    </div>

                    <!-- Password Input with Alpine Show/Hide -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Password
                            </label>
                            <a href="#" class="text-xs font-medium text-blue-900 hover:text-blue-800 transition">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                placeholder="••••••••"
                                class="block w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-300/80 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 focus:bg-white transition duration-150">
                            
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.007 10.007 0 013.682-.733c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Session Checkbox -->
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-blue-900 border-slate-300 rounded focus:ring-blue-900 cursor-pointer">
                        <label for="remember" class="ml-2.5 block text-xs font-medium text-slate-600 cursor-pointer">
                            Keep me signed in on this device
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-3.5 px-4 bg-blue-900 hover:bg-blue-950 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 transition duration-150 flex items-center justify-center space-x-2">
                        <span>Access Portal</span>
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Additional Help / Footer -->
            <p class="text-center text-xs text-slate-500">
                Need assistance? Contact IT Support at <a href="mailto:support@HOPE.ac.rw" class="text-blue-900 font-medium hover:underline">support@HOPE.ac.rw</a>
            </p>

        </div>
    </main>

    <!-- Professional Subtle Footer -->
    <footer class="py-6 border-t border-slate-200 bg-white text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs gap-2">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-blue-950">HOPE SIMS</span>
                <span>&bull;</span>
                <span>School Information Management System</span>
            </div>
            <div>
                &copy; <?= date('Y'); ?> University of Lay Adventists of Kigali. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>