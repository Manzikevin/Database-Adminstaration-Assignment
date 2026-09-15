<?php
require_once 'config.php';

$feedback = null;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code  = trim($_POST['course_code'] ?? '');
    $course_title = trim($_POST['course_title'] ?? '');
    $credits      = filter_var($_POST['credits'] ?? 3, FILTER_VALIDATE_INT);
    $dept_id      = filter_var($_POST['dept_id'] ?? null, FILTER_VALIDATE_INT);

    if (!empty($course_code) && !empty($course_title) && $credits && $dept_id) {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_title, credits, dept_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$course_code, $course_title, $credits, $dept_id]);
            $feedback = ['status' => 'success', 'message' => 'Course successfully added to master catalog!'];
        } catch (PDOException $e) {
            $feedback = ['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()];
        }
    } else {
        $feedback = ['status' => 'error', 'message' => 'Please fill in all required form parameters correctly.'];
    }
}

// Fetch Departments for dynamic menu lookup
$departments = $pdo->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course - UNILAK SIMS</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
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
                        <span class="ml-1 text-slate-500">Academic Catalog</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 font-semibold text-slate-800">Add Course</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200/80">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-950 tracking-tight">Add New Course</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-900 border border-blue-200/60">
                        Catalog Entry
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Create and register a course in the academic master catalog.</p>
            </div>
        </div>

        <!-- Form Main Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
            <form action="add_course.php" method="POST" class="divide-y divide-slate-100">
                
                <!-- Internal Section Header -->
                <div class="p-6 sm:p-8 bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-900 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Course Information</h2>
                            <p class="text-xs text-slate-500">Enter the official institutional details for this course entity.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Controls Grid -->
                <div class="p-6 sm:p-8 space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Course Code -->
                        <div>
                            <label for="course_code" class="block text-sm font-medium text-slate-700 mb-1">
                                Course Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="course_code" name="course_code" placeholder="e.g. CS101" required maxlength="10"
                                class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm font-mono tracking-wider uppercase transition duration-150">
                            <p class="mt-1.5 text-xs text-slate-500">Unique identifier assigned to the course.</p>
                        </div>

                        <!-- Course Title -->
                        <div>
                            <label for="course_title" class="block text-sm font-medium text-slate-700 mb-1">
                                Course Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="course_title" name="course_title" placeholder="e.g. Relational Database Systems" required maxlength="150"
                                class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                            <p class="mt-1.5 text-xs text-slate-500">Official name of the course as registered in the curriculum.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Credits -->
                        <div>
                            <label for="credits" class="block text-sm font-medium text-slate-700 mb-1">
                                Credit Hours <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="credits" name="credits" value="3" min="1" max="6" required
                                class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                            <p class="mt-1.5 text-xs text-slate-500">Typically between 1–6 academic credits.</p>
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="dept_id" class="block text-sm font-medium text-slate-700 mb-1">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="dept_id" name="dept_id" required 
                                    class="w-full h-11 px-4 pr-10 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm bg-white transition duration-150 appearance-none">
                                    <option value="">Select Department...</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= htmlspecialchars($dept['dept_id']); ?>">
                                            <?= htmlspecialchars($dept['dept_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500">Academic department offering this course.</p>
                        </div>
                    </div>

                    <!-- Informational Context Panel -->
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-900 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <span class="font-semibold text-slate-800">Academic Catalog Notice:</span>
                            Courses registered here become part of the master academic catalog. Ensure the course code and department details are verified before submitting.
                        </div>
                    </div>

                </div>

                <!-- Action Footer Bar -->
                <div class="px-6 py-4 sm:px-8 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <span class="text-xs text-slate-500">Changes will be added to the academic master catalog.</span>
                    <div class="flex items-center space-x-3 self-end sm:self-auto">
                        <a href="index.php" class="px-4 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg text-xs font-semibold text-white bg-blue-900 hover:bg-blue-800 active:bg-blue-950 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-900">
                            Register Course
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
                <span>Academic Management System</span>
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