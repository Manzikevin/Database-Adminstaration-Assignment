<?php
require_once 'config.php';

// Quick Stat Aggregations
$deptCount   = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
$courseCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$classCount  = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$enrollCount = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - UNILAK SIMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans text-slate-800 min-h-full flex flex-col antialiased selection:bg-blue-900 selection:text-white">

    <!-- Header Navigation -->
    <header class="bg-blue-900 text-white border-b border-blue-950/20 shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <!-- Brand Badge -->
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-blue-800 rounded-lg flex items-center justify-center font-bold text-white shadow-inner border border-blue-700/50">
                    <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
                <div class="flex items-baseline space-x-2">
                    <a href="index.php" class="text-lg font-bold tracking-tight text-white hover:text-blue-100 transition">UNILAK SIMS</a>
                    <span class="hidden md:inline-block text-xs text-blue-200 border-l border-blue-700/60 pl-2 font-normal">Academic Management System</span>
                </div>
            </div>

            <!-- Main Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1 text-xs font-medium">
                <a href="index.php" class="bg-blue-800/80 text-white px-3 py-2 rounded-md border border-blue-700/50 font-semibold transition">
                    Dashboard
                </a>
                <a href="view_records.php" class="text-blue-100 hover:text-white hover:bg-blue-800/50 px-3 py-2 rounded-md transition">
                    View Records
                </a>
                <a href="add_course.php" class="text-blue-100 hover:text-white hover:bg-blue-800/50 px-3 py-2 rounded-md transition">
                    Add Course
                </a>
                <a href="schedule_class.php" class="text-blue-100 hover:text-white hover:bg-blue-800/50 px-3 py-2 rounded-md transition">
                    Schedule Class
                </a>
                <a href="enroll_student.php" class="text-blue-100 hover:text-white hover:bg-blue-800/50 px-3 py-2 rounded-md transition">
                    Enroll Student
                </a>
            </nav>

            <!-- Mobile Menu Quick Button -->
            <div class="md:hidden">
                <a href="view_records.php" class="text-xs font-semibold text-blue-100 bg-blue-800/60 hover:bg-blue-800 px-3 py-2 rounded-md border border-blue-700/50 transition">
                    Explorer &rarr;
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8">

        <!-- Page Welcome Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-200/80 gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-950 tracking-tight">Academic Portal Dashboard</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                        System Online
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Overview of institutional entities, active terms, and administration tasks.</p>
            </div>

            <!-- Quick Explorer Action -->
            <div class="flex items-center space-x-3">
                <a href="view_records.php" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-lg text-xs font-semibold text-blue-950 bg-white border border-slate-300 shadow-sm hover:bg-slate-50 transition duration-150">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Master Data Explorer</span>
                </a>
            </div>
        </div>

        <!-- Live Metrics Bar -->
        <section aria-labelledby="metrics-heading">
            <h2 id="metrics-heading" class="sr-only">System Overview Metrics</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

                <!-- Metric 1: Departments -->
                <div class="bg-white p-6 rounded-xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Departments</span>
                        <p class="text-3xl font-bold text-blue-950 mt-1"><?= htmlspecialchars($deptCount); ?></p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Active academic units</span>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center text-blue-900 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m-4 4V7" />
                        </svg>
                    </div>
                </div>

                <!-- Metric 2: Courses -->
                <div class="bg-white p-6 rounded-xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Catalog Courses</span>
                        <p class="text-3xl font-bold text-blue-950 mt-1"><?= htmlspecialchars($courseCount); ?></p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Master curriculum entities</span>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center text-blue-900 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>

                <!-- Metric 3: Scheduled Classes -->
                <div class="bg-white p-6 rounded-xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Scheduled Classes</span>
                        <p class="text-3xl font-bold text-emerald-700 mt-1"><?= htmlspecialchars($classCount); ?></p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Active term offerings</span>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-center text-emerald-800 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <!-- Metric 4: Active Enrollments -->
                <div class="bg-white p-6 rounded-xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Enrollments</span>
                        <p class="text-3xl font-bold text-blue-950 mt-1"><?= htmlspecialchars($enrollCount); ?></p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Total student bindings</span>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center text-blue-900 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

            </div>
        </section>

        <!-- Primary Core Administrative Tasks -->
        <section aria-labelledby="tasks-heading" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 id="tasks-heading" class="text-lg font-bold text-slate-900">Core Administrative Tasks</h2>
                    <p class="text-xs text-slate-500">Execute key database transactions and catalog updates.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Task Card 1: Catalog Processing -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 p-6 flex flex-col justify-between hover:border-blue-900/30 transition duration-150">
                    <div>
                        <div class="w-10 h-10 bg-blue-50 border border-blue-100 text-blue-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900 mb-1">1. Catalog Processing</h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">Create courses assigned to departments into database schema (<code class="text-slate-700 bg-slate-100 px-1 py-0.5 rounded">add_course.php</code>).</p>
                    </div>
                    <a href="add_course.php" class="w-full inline-flex items-center justify-center space-x-2 bg-blue-900 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg text-xs shadow-sm transition duration-150">
                        <span>Add Course</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                <!-- Task Card 2: Class Scheduling -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 p-6 flex flex-col justify-between hover:border-emerald-600/30 transition duration-150">
                    <div>
                        <div class="w-10 h-10 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900 mb-1">2. Class Scheduling</h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">Bridge courses, faculty instructors, and room allocations (<code class="text-slate-700 bg-slate-100 px-1 py-0.5 rounded">schedule_class.php</code>).</p>
                    </div>
                    <a href="schedule_class.php" class="w-full inline-flex items-center justify-center space-x-2 bg-emerald-700 hover:bg-emerald-600 text-white font-semibold py-2.5 px-4 rounded-lg text-xs shadow-sm transition duration-150">
                        <span>Schedule Class</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                <!-- Task Card 3: Student Enrollment -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 p-6 flex flex-col justify-between hover:border-blue-900/30 transition duration-150">
                    <div>
                        <div class="w-10 h-10 bg-blue-50 border border-blue-100 text-blue-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900 mb-1">3. Student Enrollment</h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">Atomic dual-table transaction for student registration (<code class="text-slate-700 bg-slate-100 px-1 py-0.5 rounded">enroll_student.php</code>).</p>
                    </div>
                    <a href="enroll_student.php" class="w-full inline-flex items-center justify-center space-x-2 bg-blue-900 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg text-xs shadow-sm transition duration-150">
                        <span>Enroll Student</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

            </div>
        </section>

        <!-- Management Utilities & Data Explorer -->
        <section aria-labelledby="utilities-heading" class="space-y-4">
            <div>
                <h2 id="utilities-heading" class="text-lg font-bold text-slate-900">Entity Pre-requisite Managers & Data Explorer</h2>
                <p class="text-xs text-slate-500">Manage foundational database records and inspect complete relational tables.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Entity 1: Departments -->
                <a href="manage_departments.php" class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm hover:border-blue-900/40 hover:shadow-md transition duration-150 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 group-hover:bg-blue-50 group-hover:text-blue-900 flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m-4 4V7" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700 group-hover:text-blue-950 transition">Departments</span>
                    </div>
                    <span class="text-[11px] font-medium bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md group-hover:bg-blue-100 group-hover:text-blue-900 transition">Manage &rarr;</span>
                </a>

                <!-- Entity 2: Instructors -->
                <a href="manage_instructors.php" class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm hover:border-blue-900/40 hover:shadow-md transition duration-150 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 group-hover:bg-blue-50 group-hover:text-blue-900 flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700 group-hover:text-blue-950 transition">Faculty Instructors</span>
                    </div>
                    <span class="text-[11px] font-medium bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md group-hover:bg-blue-100 group-hover:text-blue-900 transition">Manage &rarr;</span>
                </a>

                <!-- Entity 3: Classrooms -->
                <a href="manage_classrooms.php" class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm hover:border-blue-900/40 hover:shadow-md transition duration-150 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 group-hover:bg-blue-50 group-hover:text-blue-900 flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700 group-hover:text-blue-950 transition">Classrooms</span>
                    </div>
                    <span class="text-[11px] font-medium bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md group-hover:bg-blue-100 group-hover:text-blue-900 transition">Manage &rarr;</span>
                </a>

                <!-- Entity 4: Data Explorer -->
                <a href="view_records.php" class="bg-blue-50/70 p-4 rounded-xl border border-blue-200/80 shadow-sm hover:bg-blue-100/80 transition duration-150 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-900 text-white flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-blue-950">Master Data Explorer</span>
                    </div>
                    <span class="text-[11px] font-semibold bg-blue-900 text-white px-2.5 py-1 rounded-md transition">View All &rarr;</span>
                </a>

            </div>
        </section>

    </main>

    <!-- Professional Subtle Footer -->
    <footer class="mt-auto py-6 border-t border-slate-200 bg-white text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs gap-2">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-blue-950">UNILAK SIMS</span>
                <span>&bull;</span>
                <span>School Information Management System</span>
            </div>
            <div>
                &copy; <?= date('Y'); ?> University of Lay Adventists of Kigali. Relational Database Project.
            </div>
        </div>
    </footer>

</body>

</html>