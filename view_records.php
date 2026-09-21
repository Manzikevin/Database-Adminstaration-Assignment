<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'config.php';

// Fetch Relational Views
$courses = $pdo->query("SELECT c.*, d.dept_name FROM courses c LEFT JOIN departments d ON c.dept_id = d.dept_id ORDER BY c.course_code ASC")->fetchAll();

$schedules = $pdo->query("SELECT cl.class_id, cl.semester, co.course_code, co.course_title, 
                                 CONCAT(i.first_name, ' ', i.last_name) AS instructor, r.room_name 
                          FROM classes cl 
                          JOIN courses co ON cl.course_id = co.course_id 
                          JOIN instructors i ON cl.instructor_id = i.instructor_id 
                          JOIN classrooms r ON cl.room_id = r.room_id 
                          ORDER BY cl.semester DESC")->fetchAll();

$enrollments = $pdo->query("SELECT e.enrollment_id, e.enroll_status, 
                                    CONCAT(s.first_name, ' ', s.last_name) AS student_name, s.email, 
                                    co.course_code, co.course_title, cl.semester,
                                    g.numeric_score, g.letter_grade
                             FROM enrollments e 
                             JOIN students s ON e.student_id = s.student_id 
                             JOIN classes cl ON e.class_id = cl.class_id 
                             JOIN courses co ON cl.course_id = co.course_id 
                             LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
                             ORDER BY e.enrollment_id DESC")->fetchAll();

try {
    $grades = $pdo->query("SELECT g.grade_id, g.numeric_score, g.letter_grade, 
                                  CONCAT(s.first_name, ' ', s.last_name) AS student_name, s.email, 
                                  co.course_code, co.course_title, cl.semester 
                           FROM grades g 
                           JOIN enrollments e ON g.enrollment_id = e.enrollment_id 
                           JOIN students s ON e.student_id = s.student_id 
                           JOIN classes cl ON e.class_id = cl.class_id 
                           JOIN courses co ON cl.course_id = co.course_id 
                           ORDER BY g.grade_id DESC")->fetchAll();
} catch (PDOException $e) {
    die("Query Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Explorer - HOPE SIMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans text-slate-800 min-h-full flex flex-col antialiased selection:bg-blue-900 selection:text-white" x-data="{ tab: 'enrollments' }">

    <!-- Application Top Bar Header -->
    <header class="bg-blue-900 text-white border-b border-blue-950/20 shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <!-- Brand & Identifier -->
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-blue-800 rounded-lg flex items-center justify-center font-bold text-white shadow-inner border border-blue-700/50">
                    <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
                <div class="flex items-baseline space-x-2">
                    <a href="index.php" class="text-lg font-bold tracking-tight text-white hover:text-blue-100 transition">HOPE SIMS</a>
                    <span class="hidden sm:inline-block text-xs text-blue-200 border-l border-blue-700/60 pl-2 font-normal">Academic Management System</span>
                </div>
            </div>

            <!-- Header Action -->
            <a href="index.php" class="inline-flex items-center space-x-2 text-xs font-semibold text-blue-100 hover:text-white bg-blue-800/60 hover:bg-blue-800 px-3 py-2 rounded-md border border-blue-700/50 transition duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

        <!-- Breadcrumb Navigation -->
        <nav class="flex text-xs font-medium text-slate-500 mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 sm:space-x-2">
                <li class="inline-flex items-center">
                    <a href="index.php" class="hover:text-blue-900 transition">Dashboard</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-slate-500">Reports & Analytics</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 font-semibold text-slate-800">Master Data Explorer</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header & Tab Controls Row -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-200/80 gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-950 tracking-tight">Master Relational Records</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-900 border border-blue-200/60">
                        Data Explorer
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Unified access portal for student enrollments, academic grades, scheduled class offerings, and course catalog views.</p>
            </div>

            <!-- Navigation Segmented Tabs -->
            <div class="inline-flex p-1 bg-slate-200/80 rounded-xl border border-slate-300/60 self-start md:self-auto text-xs font-semibold">
                <button @click="tab = 'enrollments'"
                    :class="tab === 'enrollments' ? 'bg-white text-blue-950 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                    class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg transition duration-150 focus:outline-none">
                    <span>Enrollments</span>
                    <span :class="tab === 'enrollments' ? 'bg-blue-100 text-blue-900' : 'bg-slate-300/70 text-slate-700'" class="px-2 py-0.5 rounded-full text-[11px] font-bold">
                        <?= count($enrollments); ?>
                    </span>
                </button>

                <button @click="tab = 'grades'"
                    :class="tab === 'grades' ? 'bg-white text-purple-950 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                    class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg transition duration-150 focus:outline-none">
                    <span>Student Grades</span>
                    <span :class="tab === 'grades' ? 'bg-purple-100 text-purple-900' : 'bg-slate-300/70 text-slate-700'" class="px-2 py-0.5 rounded-full text-[11px] font-bold">
                        <?= count($grades); ?>
                    </span>
                </button>

                <button @click="tab = 'schedules'"
                    :class="tab === 'schedules' ? 'bg-white text-blue-950 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                    class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg transition duration-150 focus:outline-none">
                    <span>Class Schedules</span>
                    <span :class="tab === 'schedules' ? 'bg-blue-100 text-blue-900' : 'bg-slate-300/70 text-slate-700'" class="px-2 py-0.5 rounded-full text-[11px] font-bold">
                        <?= count($schedules); ?>
                    </span>
                </button>

                <button @click="tab = 'courses'"
                    :class="tab === 'courses' ? 'bg-white text-blue-950 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                    class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg transition duration-150 focus:outline-none">
                    <span>Course Catalog</span>
                    <span :class="tab === 'courses' ? 'bg-blue-100 text-blue-900' : 'bg-slate-300/70 text-slate-700'" class="px-2 py-0.5 rounded-full text-[11px] font-bold">
                        <?= count($courses); ?>
                    </span>
                </button>
            </div>
        </div>

        <!-- Enrollments Tab -->
        <div x-show="tab === 'enrollments'" class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
            <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Active Student Enrollments & Evaluation Status</h2>
                        <p class="text-xs text-slate-500">Live student registrations linked to scheduled course sections and recorded marks.</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                    Total: <?= count($enrollments); ?>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Student Name</th>
                            <th class="py-3.5 px-6">Email Address</th>
                            <th class="py-3.5 px-6">Enrolled Course</th>
                            <th class="py-3.5 px-6">Semester</th>
                            <th class="py-3.5 px-6">Grade</th>
                            <th class="py-3.5 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (!empty($enrollments)): ?>
                            <?php foreach ($enrollments as $e): ?>
                                <tr class="hover:bg-slate-50/70 transition duration-150">
                                    <td class="py-3.5 px-6 font-semibold text-slate-900">
                                        <?= htmlspecialchars($e['student_name']); ?>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600 font-mono text-xs">
                                        <?= htmlspecialchars($e['email']); ?>
                                    </td>
                                    <td class="py-3.5 px-6 font-medium text-blue-950">
                                        <span class="font-bold text-blue-900 me-1"><?= htmlspecialchars($e['course_code']); ?></span>
                                        <span class="text-slate-600 font-normal">- <?= htmlspecialchars($e['course_title']); ?></span>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600 text-xs font-medium">
                                        <?= htmlspecialchars($e['semester']); ?>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <?php if ($e['letter_grade']): ?>
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded border text-xs font-bold bg-purple-50 text-purple-900 border-purple-200">
                                                <span><?= htmlspecialchars($e['letter_grade']); ?></span>
                                                <span class="text-[10px] text-purple-600 font-normal">(<?= htmlspecialchars($e['numeric_score']); ?>%)</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 italic">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200/60">
                                            <?= htmlspecialchars($e['enroll_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-12 px-6 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="text-sm font-medium text-slate-500">No student enrollment records found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Student Grades Tab -->
        <div x-show="tab === 'grades'" x-cloak class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
            <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Student Academic Grades</h2>
                        <p class="text-xs text-slate-500">Official recorded grades and percentage marks per course enrollment.</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-200/60">
                    Total Graded: <?= count($grades); ?>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Student Name</th>
                            <th class="py-3.5 px-6">Email Address</th>
                            <th class="py-3.5 px-6">Course</th>
                            <th class="py-3.5 px-6">Semester</th>
                            <th class="py-3.5 px-6">Numeric numeric_score</th>
                            <th class="py-3.5 px-6">Letter Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (!empty($grades)): ?>
                            <?php foreach ($grades as $g): ?>
                                <tr class="hover:bg-slate-50/70 transition duration-150">
                                    <td class="py-3.5 px-6 font-semibold text-slate-900">
                                        <?= htmlspecialchars($g['student_name']); ?>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600 font-mono text-xs">
                                        <?= htmlspecialchars($g['email']); ?>
                                    </td>
                                    <td class="py-3.5 px-6 font-medium text-blue-950">
                                        <span class="font-bold text-blue-900 me-1"><?= htmlspecialchars($g['course_code']); ?></span>
                                        <span class="text-slate-600 font-normal">- <?= htmlspecialchars($g['course_title']); ?></span>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600 text-xs font-medium">
                                        <?= htmlspecialchars($g['semester']); ?>
                                    </td>
                                    <td class="py-3.5 px-6 font-semibold text-slate-800 text-xs">
                                        <?= htmlspecialchars($g['numeric_score']); ?> / 100
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200">
                                            <?= htmlspecialchars($g['letter_grade']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-12 px-6 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                                    </svg>
                                    <p class="text-sm font-medium text-slate-500">No student grade records recorded yet.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Class Schedules Tab -->
        <div x-show="tab === 'schedules'" x-cloak class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
            <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-900 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Scheduled Class Offerings</h2>
                        <p class="text-xs text-slate-500">Composite view binding instructors, classroom facilities, and course blueprints.</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-100">
                    Total: <?= count($schedules); ?>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Semester</th>
                            <th class="py-3.5 px-6">Course</th>
                            <th class="py-3.5 px-6">Assigned Instructor</th>
                            <th class="py-3.5 px-6">Facility Room</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (!empty($schedules)): ?>
                            <?php foreach ($schedules as $s): ?>
                                <tr class="hover:bg-slate-50/70 transition duration-150">
                                    <td class="py-3.5 px-6 font-semibold text-emerald-700 text-xs">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-100">
                                            <?= htmlspecialchars($s['semester']); ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 font-medium text-slate-900">
                                        <span class="font-bold text-blue-900 me-1"><?= htmlspecialchars($s['course_code']); ?></span>
                                        <span class="text-slate-600 font-normal">- <?= htmlspecialchars($s['course_title']); ?></span>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-700 font-medium">
                                        <?= htmlspecialchars($s['instructor']); ?>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600">
                                        <span class="inline-flex items-center space-x-1 text-slate-700 bg-slate-100 px-2.5 py-1 rounded border border-slate-200 text-xs font-medium">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span><?= htmlspecialchars($s['room_name']); ?></span>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-12 px-6 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm font-medium text-slate-500">No scheduled class offerings found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Course Catalog Tab -->
        <div x-show="tab === 'courses'" x-cloak class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
            <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Curriculum Master Catalog</h2>
                        <p class="text-xs text-slate-500">Approved academic courses and institutional credit allocations.</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-100">
                    Total: <?= count($courses); ?>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Course Code</th>
                            <th class="py-3.5 px-6">Course Title</th>
                            <th class="py-3.5 px-6">Credit Weight</th>
                            <th class="py-3.5 px-6">Department</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $c): ?>
                                <tr class="hover:bg-slate-50/70 transition duration-150">
                                    <td class="py-3.5 px-6 font-bold text-blue-900 text-xs">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded bg-blue-50 text-blue-900 border border-blue-100 font-mono">
                                            <?= htmlspecialchars($c['course_code']); ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 font-semibold text-slate-900">
                                        <?= htmlspecialchars($c['course_title']); ?>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600 text-xs font-medium">
                                        <?= htmlspecialchars($c['credits']); ?> Credits
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <?php if (!empty($c['dept_name'])): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                <?= htmlspecialchars($c['dept_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                Unassigned
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-12 px-6 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="text-sm font-medium text-slate-500">No course catalog records found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Professional Subtle Footer -->
    <footer class="mt-auto py-6 border-t border-slate-200 bg-white text-slate-500">
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