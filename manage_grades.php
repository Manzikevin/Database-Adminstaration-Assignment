<?php
require_once 'config.php';

$feedback = null;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = filter_var($_POST['enrollment_id'] ?? null, FILTER_VALIDATE_INT);
    $numeric_score = filter_var($_POST['numeric_score'] ?? null, FILTER_VALIDATE_FLOAT);
    $letter_grade  = strtoupper(trim($_POST['letter_grade'] ?? ''));

    if ($enrollment_id && $numeric_score !== false && !empty($letter_grade)) {
        if ($numeric_score >= 0 && $numeric_score <= 100) {
            try {
                // Upsert: Insert or Update if enrollment_id grade record already exists
                $stmt = $pdo->prepare("
                    INSERT INTO grades (enrollment_id, numeric_score, letter_grade) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                        numeric_score = VALUES(numeric_score), 
                        letter_grade = VALUES(letter_grade)
                ");
                $stmt->execute([$enrollment_id, $numeric_score, $letter_grade]);
                $feedback = ['status' => 'success', 'message' => 'Student academic grade recorded successfully!'];
            } catch (PDOException $e) {
                $feedback = ['status' => 'error', 'message' => 'Database Exception: ' . $e->getMessage()];
            }
        } else {
            $feedback = ['status' => 'error', 'message' => 'Numeric score must be between 0.00 and 100.00.'];
        }
    } else {
        $feedback = ['status' => 'error', 'message' => 'All grade evaluation fields are required.'];
    }
}

// Fetch Active Enrollments with Student & Course Context
$enrollments = $pdo->query("
    SELECT 
        e.enrollment_id, 
        CONCAT(s.first_name, ' ', s.last_name) AS student_name, 
        s.email,
        co.course_code, 
        co.course_title, 
        cl.semester,
        g.grade_id,
        g.numeric_score,
        g.letter_grade
    FROM enrollments e
    JOIN students s ON e.student_id = s.student_id
    JOIN classes cl ON e.class_id = cl.class_id
    JOIN courses co ON cl.course_id = co.course_id
    LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
    ORDER BY e.enrollment_id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades - UNILAK SIMS</title>
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
                        <span class="ml-1 text-slate-500">Academic Records</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 font-semibold text-slate-800">Manage Grades</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200/80">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-950 tracking-tight">Grade Evaluation Terminal</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200/60">
                        Student Assessment
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Record numeric scores and assign letter grades to student course enrollments.</p>
            </div>
        </div>

        <!-- Form Main Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
            <form action="manage_grades.php" method="POST" class="divide-y divide-slate-100">

                <!-- Internal Section Header -->
                <div class="p-6 sm:p-8 bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Grade Assignment Parameters</h2>
                            <p class="text-xs text-slate-500">Select an active enrollment record and submit student score details.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Controls Grid -->
                <div class="p-6 sm:p-8 space-y-6">

                    <!-- Student Enrollment Selection -->
                    <div>
                        <label for="enrollment_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Student Enrollment Record <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="enrollment_id" name="enrollment_id" required
                                class="w-full h-11 px-4 pr-10 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600 text-sm bg-white transition duration-150 appearance-none">
                                <option value="">Select Student Course Enrollment...</option>
                                <?php foreach ($enrollments as $enr): ?>
                                    <option value="<?= $enr['enrollment_id']; ?>">
                                        <?= htmlspecialchars($enr['student_name'] . ' — ' . $enr['course_code'] . ' (' . $enr['semester'] . ')'); ?>
                                        <?= $enr['grade_id'] ? ' [Current Grade: ' . htmlspecialchars($enr['letter_grade']) . ' (' . htmlspecialchars($enr['numeric_score']) . ')]' : ' [No Grade Recorded]'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Select active student enrollment to evaluate or update.</p>
                    </div>

                    <!-- Evaluation Inputs Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Numeric Score -->
                        <div>
                            <label for="numeric_score" class="block text-sm font-medium text-slate-700 mb-1">
                                Numeric Score (0.00 - 100.00) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" max="100" id="numeric_score" name="numeric_score" placeholder="e.g. 88.50" required
                                class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600 text-sm transition duration-150">
                            <p class="mt-1.5 text-xs text-slate-500">Decimal score achieved out of 100%.</p>
                        </div>

                        <!-- Letter Grade -->
                        <div>
                            <label for="letter_grade" class="block text-sm font-medium text-slate-700 mb-1">
                                Letter Grade <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="letter_grade" name="letter_grade" required
                                    class="w-full h-11 px-4 pr-10 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600 text-sm bg-white transition duration-150 appearance-none">
                                    <option value="">Select Grade...</option>
                                    <option value="A+">A+ (90 - 100)</option>
                                    <option value="A">A (80 - 89)</option>
                                    <option value="B+">B+ (75 - 79)</option>
                                    <option value="B">B (70 - 74)</option>
                                    <option value="C+">C+ (65 - 69)</option>
                                    <option value="C">C (60 - 64)</option>
                                    <option value="D+">D+ (55 - 59)</option>
                                    <option value="D">D (50 - 54)</option>
                                    <option value="F">F (0 - 49)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500">Institutional letter grade designation.</p>
                        </div>
                    </div>

                    <!-- Informational Context Panel -->
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-purple-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-semibold text-slate-800">Relational Database Constraint:</span>
                            The <code class="font-mono bg-slate-200 px-1 py-0.5 rounded text-[11px] text-slate-800">enrollment_id</code> column enforces a strict <code class="font-mono bg-slate-200 px-1 py-0.5 rounded text-[11px] text-slate-800">UNIQUE</code> constraint. Re-submitting a grade for an existing enrollment will safely update the evaluation record.
                        </div>
                    </div>

                </div>

                <!-- Action Footer Bar -->
                <div class="px-6 py-4 sm:px-8 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <span class="text-xs text-slate-500">Form Task: Academic grade management terminal.</span>
                    <div class="flex items-center space-x-3 self-end sm:self-auto">
                        <a href="index.php" class="px-4 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg text-xs font-semibold text-white bg-purple-700 hover:bg-purple-600 active:bg-purple-800 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-purple-700">
                            Save Academic Grade
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