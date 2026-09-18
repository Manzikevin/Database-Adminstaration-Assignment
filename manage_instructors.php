<?php
require_once 'config.php';

$feedback = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name    = trim($_POST['first_name'] ?? '');
    $last_name     = trim($_POST['last_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $dept_id       = filter_var($_POST['dept_id'] ?? null, FILTER_VALIDATE_INT);
    $building_name = trim($_POST['building_name'] ?? '');
    $room_number   = trim($_POST['room_number'] ?? '');

    if (!empty($first_name) && !empty($last_name) && !empty($email)) {
        try {
            // Begin transaction to ensure atomic insertion across instructors and office_details
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO instructors (first_name, last_name, email, dept_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $dept_id ?: null]);
            $instructor_id = $pdo->lastInsertId();

            // Insert optional office details if provided
            if (!empty($building_name) && !empty($room_number)) {
                $stmt_off = $pdo->prepare("INSERT INTO office_details (instructor_id, building_name, room_number) VALUES (?, ?, ?)");
                $stmt_off->execute([$instructor_id, $building_name, $room_number]);
            }

            $pdo->commit();
            $feedback = ['status' => 'success', 'message' => 'Faculty member and office details registered successfully!'];
        } catch (PDOException $e) {
            $pdo->rollBack();
            $feedback = ['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()];
        }
    } else {
        $feedback = ['status' => 'error', 'message' => 'Please fill in all required fields.'];
    }
}

$departments = $pdo->query("SELECT * FROM departments ORDER BY dept_name ASC")->fetchAll();

// Join instructors with departments and optional 1-to-1 office_details
$instructors = $pdo->query("
    SELECT i.*, d.dept_name, o.building_name, o.room_number 
    FROM instructors i 
    LEFT JOIN departments d ON i.dept_id = d.dept_id 
    LEFT JOIN office_details o ON i.instructor_id = o.instructor_id 
    ORDER BY i.instructor_id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Instructors - UNILAK SIMS</title>
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
                        <span class="ml-1 text-slate-500">Academic Structure</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 font-semibold text-slate-800">Instructors</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200/80">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-950 tracking-tight">Faculty Instructors</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-900 border border-blue-200/60">
                        Entity Manager
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Manage academic staff, department assignments, office locations, and contact details.</p>
            </div>
        </div>

        <!-- 2-Column Desktop Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <!-- Left Column: Registration Form Panel -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden lg:sticky lg:top-24">
                <div class="p-6 bg-slate-50/50 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-900 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Add Faculty Instructor</h2>
                            <p class="text-xs text-slate-500">Register a new faculty member.</p>
                        </div>
                    </div>
                </div>

                <form action="manage_instructors.php" method="POST" class="p-6 space-y-5">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required maxlength="50" placeholder="e.g. Marie"
                            class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                        <p class="mt-1.5 text-xs text-slate-500">Instructor's given name.</p>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" required maxlength="50" placeholder="e.g. Uwimana"
                            class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                        <p class="mt-1.5 text-xs text-slate-500">Instructor's surname.</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required maxlength="100" placeholder="e.g. m.uwimana@unilak.ac.rw"
                            class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                        <p class="mt-1.5 text-xs text-slate-500">Institutional email for academic communication.</p>
                    </div>

                    <!-- Department Select -->
                    <div>
                        <label for="dept_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Department
                        </label>
                        <div class="relative">
                            <select id="dept_id" name="dept_id"
                                class="w-full h-11 px-4 pr-10 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm bg-white transition duration-150 appearance-none">
                                <option value="">Unassigned</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['dept_id']; ?>"><?= htmlspecialchars($d['dept_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Associated academic department.</p>
                    </div>

                    <!-- Optional Office Details Section -->
                    <div class="pt-3 border-t border-slate-200/80 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Office Details</span>
                            <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Optional</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="building_name" class="block text-xs font-medium text-slate-700 mb-1">Building</label>
                                <input type="text" id="building_name" name="building_name" maxlength="50" placeholder="e.g. Block A"
                                    class="w-full h-10 px-3 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                            </div>

                            <div>
                                <label for="room_number" class="block text-xs font-medium text-slate-700 mb-1">Room No.</label>
                                <input type="text" id="room_number" name="room_number" maxlength="10" placeholder="e.g. 204"
                                    class="w-full h-10 px-3 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                            </div>
                        </div>
                        <p class="text-xs text-slate-500">Physical office allocation for faculty consultation hours.</p>
                    </div>

                    <!-- Form Action Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full h-11 inline-flex items-center justify-center space-x-2 rounded-lg text-xs font-semibold text-white bg-blue-900 hover:bg-blue-800 active:bg-blue-950 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Register Instructor</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column: Registered Instructors Table -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Faculty Roster</h2>
                        <p class="text-xs text-slate-500">Active instructors, department assignments, and assigned office spaces.</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        Total Faculty: <?= count($instructors); ?>
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-6">Faculty Member</th>
                                <th class="py-3 px-6">Email Address</th>
                                <th class="py-3 px-6">Department</th>
                                <th class="py-3 px-6">Office Allocation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (!empty($instructors)): ?>
                                <?php foreach ($instructors as $inst): ?>
                                    <tr class="hover:bg-slate-50/70 transition duration-150">
                                        <td class="py-3.5 px-6 font-semibold text-slate-900 flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-900 text-white flex items-center justify-center shrink-0 text-xs font-semibold shadow-xs">
                                                <?= strtoupper(substr($inst['first_name'], 0, 1) . substr($inst['last_name'], 0, 1)); ?>
                                            </div>
                                            <span><?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']); ?></span>
                                        </td>
                                        <td class="py-3.5 px-6 text-slate-600 font-mono text-xs">
                                            <?= htmlspecialchars($inst['email']); ?>
                                        </td>
                                        <td class="py-3.5 px-6">
                                            <?php if (!empty($inst['dept_name'])): ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-900 border border-blue-100">
                                                    <?= htmlspecialchars($inst['dept_name']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                    Unassigned
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3.5 px-6">
                                            <?php if (!empty($inst['building_name'])): ?>
                                                <span class="inline-flex items-center space-x-1.5 text-slate-700 bg-slate-100 px-2.5 py-1 rounded border border-slate-200 text-xs font-medium">
                                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    <span><?= htmlspecialchars($inst['building_name'] . ' — Rm ' . $inst['room_number']); ?></span>
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                                    No Office Assigned
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-12 px-6 text-center text-slate-400">
                                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No faculty members found.</p>
                                        <p class="text-xs text-slate-400 mt-1">Use the form on the left to add your first instructor.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

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