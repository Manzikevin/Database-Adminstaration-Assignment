<?php
require_once 'config.php';

$feedback = null;

// Form Submission with Explicit Transaction Block
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name      = trim($_POST['first_name'] ?? '');
    $last_name       = trim($_POST['last_name'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $enrollment_date = $_POST['enrollment_date'] ?? date('Y-m-d');
    $class_id        = filter_var($_POST['class_id'] ?? null, FILTER_VALIDATE_INT);

    if (!empty($first_name) && !empty($last_name) && !empty($email) && $class_id) {
        try {
            // Open isolated transaction sequence handle
            $pdo->beginTransaction();

            // 1. Insert student demographic record
            $stmtStudent = $pdo->prepare("INSERT INTO students (first_name, last_name, email, enrollment_date) VALUES (?, ?, ?, ?)");
            $stmtStudent->execute([$first_name, $last_name, $email, $enrollment_date]);

            // 2. Isolate unique system-generated Student ID
            $student_id = $pdo->lastInsertId();

            // 3. Create secondary enrollment entry using captured Student ID
            $stmtEnrollment = $pdo->prepare("INSERT INTO enrollments (student_id, class_id, enroll_status) VALUES (?, ?, 'Active')");
            $stmtEnrollment->execute([$student_id, $class_id]);

            // 4. Commit operations safely
            $pdo->commit();

            $feedback = ['status' => 'success', 'message' => "Student successfully registered & enrolled (ID: #$student_id)!"];
        } catch (Exception $e) {
            // Initiate rollback on constraint failure
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $feedback = ['status' => 'error', 'message' => 'Transaction Failed & Rolled Back: ' . $e->getMessage()];
        }
    } else {
        $feedback = ['status' => 'error', 'message' => 'Validation error: Fill all required fields.'];
    }
}

// Fetch active classes joined with course names for readability
$sqlClasses = "SELECT cl.class_id, cl.semester, co.course_code, co.course_title 
               FROM classes cl 
               JOIN courses co ON cl.course_id = co.course_id 
               ORDER BY cl.semester DESC, co.course_code ASC";
$activeClasses = $pdo->query($sqlClasses)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Student - UNILAK SIMS</title>
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
                        <span class="ml-1 text-slate-500">Student Administration</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 font-semibold text-slate-800">Enroll Student</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200/80">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-950 tracking-tight">Enroll New Student</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-900 border border-blue-200/60">
                        Atomic Transaction
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Register student record and bind to a scheduled class section in a single transaction.</p>
            </div>
        </div>

        <!-- Form Main Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
            <form action="enroll_student.php" method="POST" class="divide-y divide-slate-100">

                <!-- Internal Section Header -->
                <div class="p-6 sm:p-8 bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-900 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Student & Enrollment Information</h2>
                            <p class="text-xs text-slate-500">Enter demographic details and assign the student to an active course section.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Controls Grid -->
                <div class="p-6 sm:p-8 space-y-6">

                    <!-- Names Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" required maxlength="50" placeholder="e.g. Jean"
                                class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                            <p class="mt-1.5 text-xs text-slate-500">Student's official first name.</p>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" required maxlength="50" placeholder="e.g. Mugisha"
                                class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                            <p class="mt-1.5 text-xs text-slate-500">Student's official surname or family name.</p>
                        </div>
                    </div>

                    <!-- Email & Registration Date Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required maxlength="100" placeholder="e.g. j.mugisha@unilak.ac.rw"
                                class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                            <p class="mt-1.5 text-xs text-slate-500">Institutional or primary student email.</p>
                        </div>

                        <!-- Registration Date -->
                        <div>
                            <label for="enrollment_date" class="block text-sm font-medium text-slate-700 mb-1">
                                Registration Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="enrollment_date" name="enrollment_date" value="<?= date('Y-m-d'); ?>" required
                                class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                            <p class="mt-1.5 text-xs text-slate-500">Official date of student registration.</p>
                        </div>
                    </div>

                    <!-- Class Select -->
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Target Scheduled Class <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="class_id" name="class_id" required
                                class="w-full h-11 px-4 pr-10 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm bg-white transition duration-150 appearance-none">
                                <option value="">Select Scheduled Class Option...</option>
                                <?php foreach ($activeClasses as $cls): ?>
                                    <option value="<?= $cls['class_id']; ?>">
                                        <?= htmlspecialchars($cls['course_code'] . ' - ' . $cls['course_title'] . ' (' . $cls['semester'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Select an active class offering to bind the student enrollment.</p>
                    </div>

                    <!-- Informational Context Panel -->
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-900 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-semibold text-slate-800">Transactional Integrity Guarantee:</span>
                            This action inserts the student record and binds it to the selected class within an atomic PDO transaction. If any database constraint fails, all operations will be automatically rolled back.
                        </div>
                    </div>

                </div>

                <!-- Action Footer Bar -->
                <div class="px-6 py-4 sm:px-8 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <span class="text-xs text-slate-500">Atomic registration and multi-table transaction handle.</span>
                    <div class="flex items-center space-x-3 self-end sm:self-auto">
                        <a href="index.php" class="px-4 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg text-xs font-semibold text-white bg-blue-900 hover:bg-blue-800 active:bg-blue-950 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-900">
                            Execute Transactional Enrollment
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