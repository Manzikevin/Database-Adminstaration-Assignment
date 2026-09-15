<?php
require_once 'config.php';

$feedback = null;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id     = filter_var($_POST['course_id'] ?? null, FILTER_VALIDATE_INT);
    $instructor_id = filter_var($_POST['instructor_id'] ?? null, FILTER_VALIDATE_INT);
    $room_id       = filter_var($_POST['room_id'] ?? null, FILTER_VALIDATE_INT);
    $semester      = trim($_POST['semester'] ?? '');

    if ($course_id && $instructor_id && $room_id && !empty($semester)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO classes (course_id, instructor_id, room_id, semester) VALUES (?, ?, ?, ?)");
            $stmt->execute([$course_id, $instructor_id, $room_id, $semester]);
            $feedback = ['status' => 'success', 'message' => 'Class scheduled successfully in composite bridge table!'];
        } catch (PDOException $e) {
            $feedback = ['status' => 'error', 'message' => 'Database Constraint Failure: ' . $e->getMessage()];
        }
    } else {
        $feedback = ['status' => 'error', 'message' => 'All selection fields are required.'];
    }
}

// Fetch Lookups
$courses     = $pdo->query("SELECT course_id, course_code, course_title FROM courses ORDER BY course_code ASC")->fetchAll();
$instructors = $pdo->query("SELECT instructor_id, CONCAT(first_name, ' ', last_name) AS full_name FROM instructors ORDER BY last_name ASC")->fetchAll();
$classrooms  = $pdo->query("SELECT room_id, room_name, capacity FROM classrooms ORDER BY room_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Class - UNILAK SIMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans text-slate-800 min-h-full flex flex-col antialiased selection:bg-blue-900 selection:text-white">

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
                    <a href="index.php" class="text-lg font-bold tracking-tight text-white hover:text-blue-100 transition">UNILAK SIMS</a>
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
    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

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
                        <span class="ml-1 text-slate-500">Course Administration</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 font-semibold text-slate-800">Schedule Class</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200/80">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-950 tracking-tight">Class Scheduling Terminal</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200/60">
                        Multi-Bridge Allocator
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Bind catalog course blueprints, faculty instructors, and room facilities into active term allocations.</p>
            </div>
        </div>

        <!-- Form Main Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
            <form action="schedule_class.php" method="POST" class="divide-y divide-slate-100">

                <!-- Internal Section Header -->
                <div class="p-6 sm:p-8 bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Class Allocation Parameters</h2>
                            <p class="text-xs text-slate-500">Configure academic term schedules and bind required relational keys.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Controls Grid -->
                <div class="p-6 sm:p-8 space-y-6">

                    <!-- Semester Term -->
                    <div>
                        <label for="semester" class="block text-sm font-medium text-slate-700 mb-1">
                            Semester Term <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="semester" name="semester" placeholder="e.g. Fall 2026" required
                            class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-sm transition duration-150">
                        <p class="mt-1.5 text-xs text-slate-500">Target academic term (e.g. Fall 2026, Semester I 2026/2027).</p>
                    </div>

                    <!-- Course Blueprint -->
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Course Blueprint <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="course_id" name="course_id" required
                                class="w-full h-11 px-4 pr-10 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-sm bg-white transition duration-150 appearance-none">
                                <option value="">Select Course...</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['course_id']; ?>">
                                        <?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Select active course offering from official academic catalog.</p>
                    </div>

                    <!-- Assigned Instructor -->
                    <div>
                        <label for="instructor_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Assigned Instructor <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="instructor_id" name="instructor_id" required
                                class="w-full h-11 px-4 pr-10 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-sm bg-white transition duration-150 appearance-none">
                                <option value="">Select Faculty Member...</option>
                                <?php foreach ($instructors as $inst): ?>
                                    <option value="<?= $inst['instructor_id']; ?>">
                                        <?= htmlspecialchars($inst['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Assign a registered faculty member to lead the scheduled class.</p>
                    </div>

                    <!-- Classroom Facility -->
                    <div>
                        <label for="room_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Classroom Facility <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="room_id" name="room_id" required
                                class="w-full h-11 px-4 pr-10 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-sm bg-white transition duration-150 appearance-none">
                                <option value="">Select Facility Room...</option>
                                <?php foreach ($classrooms as $rm): ?>
                                    <option value="<?= $rm['room_id']; ?>">
                                        <?= htmlspecialchars($rm['room_name'] . ' (Cap: ' . $rm['capacity'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Assign physical room or laboratory facility with seating capacity check.</p>
                    </div>

                    <!-- Informational Context Panel -->
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-semibold text-slate-800">Relational Bridge Junction:</span>
                            This operation creates an entry in the <code class="font-mono bg-slate-200 px-1 py-0.5 rounded text-[11px] text-slate-800">classes</code> bridge table, binding foreign keys for course, instructor, and classroom to instantiate a scheduled term section.
                        </div>
                    </div>

                </div>

                <!-- Action Footer Bar -->
                <div class="px-6 py-4 sm:px-8 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <span class="text-xs text-slate-500">Form Task 2: Multi-bridge scheduling junction.</span>
                    <div class="flex items-center space-x-3 self-end sm:self-auto">
                        <a href="index.php" class="px-4 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-600">
                            Confirm Schedule Allocation
                        </button>
                    </div>
                </div>

            </form>
        </div>
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
                &copy; <?= date('Y'); ?> University of Lay Adventists of Kigali. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- SweetAlert Toast Notifications -->
    <script>
        <?php if ($feedback): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: '<?= $feedback['status']; ?>',
                title: '<?= addslashes($feedback['message']); ?>',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
        <?php endif; ?>
    </script>
</body>

</html>