<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOPE SIMS - Student & Academic Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom Keyframe Animations */
        @keyframes floatSlow {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: floatSlow 4s ease-in-out infinite;
        }

        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>

<body class="font-sans text-slate-800 min-h-full flex flex-col antialiased selection:bg-blue-900 selection:text-white bg-slate-50" x-data="{ mobileMenuOpen: false }">

    <!-- Navigation Bar -->
    <header class="bg-blue-900/95 backdrop-blur-md text-white border-b border-blue-800/50 shadow-md sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            <!-- Brand Logo -->
            <a href="index.php" class="flex items-center space-x-3 group">
                <div class="w-11 h-11 bg-white/10 rounded-xl flex items-center justify-center font-bold text-white border border-white/20 group-hover:bg-white group-hover:text-blue-900 transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight text-white leading-tight">HOPE SIMS</span>
                    <span class="text-xs text-blue-200 font-normal">Student & Campus Portal</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-blue-100">
                <a href="#about" class="hover:text-white transition-colors duration-200">About Us</a>
                <a href="#features" class="hover:text-white transition-colors duration-200">Features</a>
                <a href="#stats" class="hover:text-white transition-colors duration-200">Campus Highlights</a>
                <a href="#contact" class="hover:text-white transition-colors duration-200">Contact & Support</a>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php" class="inline-flex items-center space-x-2 text-xs font-semibold text-blue-950 bg-white hover:bg-blue-50 px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Go to Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="inline-flex items-center space-x-2 text-xs font-semibold text-white bg-blue-800/80 hover:bg-blue-700 px-5 py-2.5 rounded-xl border border-blue-600/50 transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Sign In</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden p-2 text-blue-200 hover:text-white focus:outline-none">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-blue-950 border-t border-blue-800/60 px-4 pt-3 pb-6 space-y-3">
            <a href="#about" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-blue-100 hover:text-white hover:bg-blue-900 transition">About Us</a>
            <a href="#features" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-blue-100 hover:text-white hover:bg-blue-900 transition">Features</a>
            <a href="#stats" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-blue-100 hover:text-white hover:bg-blue-900 transition">Highlights</a>
            <a href="#contact" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-blue-100 hover:text-white hover:bg-blue-900 transition">Contact & Support</a>
            <div class="pt-2 border-t border-blue-800/60">
                <?php if ($isLoggedIn): ?>
                    <a href="master_explorer.php" class="w-full inline-flex justify-center items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-white text-blue-900">
                        Go to Dashboard
                    </a>
                <?php else: ?>
                    <a href="login.php" class="w-full inline-flex justify-center items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-blue-800 text-white">
                        Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hero Section with Real Campus Background Image -->
    <section class="relative bg-slate-900 text-white py-24 lg:py-36 overflow-hidden bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1920&q=80');">
        <!-- Elegant Dark Overlay for Optimal Readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-950/90 via-blue-900/85 to-slate-900/90 backdrop-blur-[2px]"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 animate-fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                <!-- Hero Text Content -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-blue-200 border border-white/20 backdrop-blur-md">
                        Welcome to the HOPE Student Portal
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-white">
                        Empowering Student Success at <span class="text-blue-300">Hope Academy</span>
                    </h1>
                    <p class="text-base sm:text-lg text-slate-200 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                        Easily manage your courses, view your grades, check timetable schedules, and stay connected with university life—all in one convenient place.
                    </p>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="login.php" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-sm font-bold text-blue-950 bg-white hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-2xl hover:-translate-y-1">
                            <span>Access Portal</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="#features" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-sm font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md transition-all duration-300 hover:-translate-y-1">
                            Explore Capabilities
                        </a>
                    </div>
                </div>

                <!-- Floating Campus Showcase Card -->
                <div class="lg:col-span-5 animate-float">
                    <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-6 sm:p-8 border border-white/20 shadow-2xl space-y-5">
                        <div class="flex items-center space-x-3 pb-4 border-b border-white/15">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Fast & Reliable</p>
                                <p class="text-xs text-blue-200">Everything you need, whenever you need it</p>
                            </div>
                        </div>

                        <!-- Feature Highlights -->
                        <div class="space-y-3">
                            <div class="p-4 bg-white/10 hover:bg-white/15 rounded-2xl border border-white/10 transition-all duration-200 flex items-center space-x-4">
                                <div class="p-2.5 bg-emerald-500/20 text-emerald-300 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">Course Registration</p>
                                    <p class="text-[11px] text-slate-300">Register for classes smoothly every term</p>
                                </div>
                            </div>

                            <div class="p-4 bg-white/10 hover:bg-white/15 rounded-2xl border border-white/10 transition-all duration-200 flex items-center space-x-4">
                                <div class="p-2.5 bg-amber-500/20 text-amber-300 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">Instant Academic Results</p>
                                    <p class="text-[11px] text-slate-300">View your latest grades and GPA progress</p>
                                </div>
                            </div>

                            <div class="p-4 bg-white/10 hover:bg-white/15 rounded-2xl border border-white/10 transition-all duration-200 flex items-center space-x-4">
                                <div class="p-2.5 bg-blue-500/20 text-blue-300 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">Class Schedules</p>
                                    <p class="text-[11px] text-slate-300">Check class times and lecture rooms</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-20 sm:py-28 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-blue-900 uppercase tracking-widest bg-blue-50 px-3.5 py-1.5 rounded-full border border-blue-100">About HOPE SIMS</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight mt-4">
                    Making University Life Simpler for Everyone
                </h2>
                <p class="mt-4 text-slate-600 text-sm sm:text-base leading-relaxed">
                    The University of Lay Adventists of Kigali (HOPE) portal helps students, lecturers, and university staff manage academic information efficiently and clearly.
                </p>
            </div>

            <!-- 3 Core Pillars -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-slate-50/80 rounded-2xl p-8 border border-slate-200/80 hover:border-blue-300 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-900 text-white rounded-2xl flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Quick & Easy Access</h3>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                        Find your class schedules, course materials, and exam results instantly without long waiting times.
                    </p>
                </div>

                <div class="bg-slate-50/80 rounded-2xl p-8 border border-slate-200/80 hover:border-blue-300 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-900 text-white rounded-2xl flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Safe & Secure</h3>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                        Your personal details, course marks, and official university records are kept safe and private at all times.
                    </p>
                </div>

                <div class="bg-slate-50/80 rounded-2xl p-8 border border-slate-200/80 hover:border-blue-300 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-900 text-white rounded-2xl flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Everything in One Place</h3>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                        Connect students, instructors, and departments through a single, friendly website that works on desktop and mobile.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 sm:py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-blue-900 uppercase tracking-widest bg-blue-100/60 px-3.5 py-1.5 rounded-full border border-blue-200/60">What You Can Do</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight mt-4">
                    Designed For Student Convenience
                </h2>
                <p class="mt-4 text-slate-600 text-sm sm:text-base">
                    Built to make every aspect of your academic journey smooth, transparent, and easy to follow.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-blue-300 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold mb-5 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Student Registration</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Sign up for classes, review enrolled subjects, and keep your student profile updated for every term.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-blue-300 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold mb-5 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Grades & Reports</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Check your latest examination results, assignment marks, and overall GPA whenever you need them.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-blue-300 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-800 flex items-center justify-center font-bold mb-5 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Class Timetables</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            View your daily class schedules, assigned lecturers, and lecture hall locations in real time.
                        </p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-blue-300 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold mb-5 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Course Guide</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Browse available study programs, credit requirements, and subject descriptions across all faculties.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Campus Highlights Banner -->
    <section id="stats" class="bg-gradient-to-r from-blue-900 via-blue-950 to-slate-900 text-white py-16 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-blue-800/80">
                <div class="pt-4 md:pt-0">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white">100%</p>
                    <p class="text-xs sm:text-sm text-blue-200 mt-1">Reliable & Secure</p>
                </div>
                <div class="pt-4 md:pt-0">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white">4+</p>
                    <p class="text-xs sm:text-sm text-blue-200 mt-1">Faculties Supported</p>
                </div>
                <div class="pt-4 md:pt-0">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white">Fast</p>
                    <p class="text-xs sm:text-sm text-blue-200 mt-1">Grade Access</p>
                </div>
                <div class="pt-4 md:pt-0">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white">24/7</p>
                    <p class="text-xs sm:text-sm text-blue-200 mt-1">Online Access</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action (CTA) Section -->
    <section class="py-20 sm:py-24 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-blue-900 to-blue-950 rounded-3xl p-8 sm:p-14 text-center text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">Ready to access your account?</h2>
                    <p class="mt-4 text-blue-200 text-xs sm:text-base max-w-xl mx-auto leading-relaxed">
                        Sign in with your official university account to check your course timetable, grades, and campus updates.
                    </p>
                    <div class="mt-8">
                        <a href="login.php" class="inline-flex items-center px-8 py-4 rounded-xl text-sm font-bold text-blue-950 bg-white hover:bg-blue-50 transition-all duration-300 shadow-xl hover:-translate-y-1">
                            <span>Sign In to Your Portal</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-12 border-b border-slate-800">

                <!-- Brand Info -->
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 bg-blue-900 rounded-xl flex items-center justify-center font-bold text-white shadow-sm">
                            <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">HOPE SIMS</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        University of Lay Adventists of Kigali — Dedicated to excellence in higher education, research, and service.
                    </p>
                </div>

                <!-- Navigation Quick Links -->
                <div class="md:col-span-3 space-y-2 text-xs">
                    <p class="font-bold text-white uppercase tracking-wider mb-3">Quick Links</p>
                    <ul class="space-y-2.5">
                        <li><a href="login.php" class="hover:text-white transition">Sign In</a></li>
                        <li><a href="#about" class="hover:text-white transition">About SIMS</a></li>
                        <li><a href="#features" class="hover:text-white transition">Portal Features</a></li>
                        <li><a href="#stats" class="hover:text-white transition">Campus Highlights</a></li>
                    </ul>
                </div>

                <!-- Support & Contact -->
                <div class="md:col-span-4 space-y-2 text-xs">
                    <p class="font-bold text-white uppercase tracking-wider mb-3">Help & Contact</p>
                    <p>Kigali Campus, Rwanda</p>
                    <p>Email: <a href="mailto:support@HOPE.ac.rw" class="text-blue-400 hover:underline">support@HOPE.ac.rw</a></p>
                    <p>Website: <a href="https://HOPE.ac.rw" target="_blank" class="text-blue-400 hover:underline">www.HOPE.ac.rw</a></p>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; <?= date('Y'); ?> University of Lay Adventists of Kigali (HOPE). All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-slate-400 transition">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-400 transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>