<?php
require_once 'config.php';

$feedback = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action =$_POST['action'] ?? '';

    if ($action === 'create') {
        $dept_name = trim($_POST['dept_name'] ?? '');
        if (!empty($dept_name)) {
            try {
                $stmt =$pdo->prepare("INSERT INTO departments (dept_name) VALUES (?)");
                $stmt->execute([$dept_name]);$feedback = ['status' => 'success', 'message' => 'Department registered successfully!'];
            } catch (PDOException $e) {
                $feedback = ['status' => 'error', 'message' => 'Error: ' .$e->getMessage()];
            }
        } else {
            $feedback = ['status' => 'error', 'message' => 'Department name is required.'];
        }
    } elseif ($action === 'update') {
        $dept_id   = filter_var($_POST['dept_id'] ?? null, FILTER_VALIDATE_INT);
        $dept_name = trim($_POST['dept_name'] ?? '');

        if ($dept_id && !empty($dept_name)) {
            try {
                $stmt =$pdo->prepare("UPDATE departments SET dept_name = ? WHERE dept_id = ?");
                $stmt->execute([$dept_name, $dept_id]);$feedback = ['status' => 'success', 'message' => 'Department updated successfully!'];
            } catch (PDOException $e) {
                $feedback = ['status' => 'error', 'message' => 'Error updating department: ' .$e->getMessage()];
            }
        } else {
            $feedback = ['status' => 'error', 'message' => 'Valid department ID and name required.'];
        }
    } elseif ($action === 'delete') {
        $dept_id = filter_var($_POST['dept_id'] ?? null, FILTER_VALIDATE_INT);

        if ($dept_id) {
            try {
                $stmt =$pdo->prepare("DELETE FROM departments WHERE dept_id = ?");
                $stmt->execute([$dept_id]);$feedback = ['status' => 'success', 'message' => 'Department deleted successfully!'];
            } catch (PDOException $e) {
                $feedback = ['status' => 'error', 'message' => 'Error deleting department: ' .$e->getMessage()];
            }
        } else {
            $feedback = ['status' => 'error', 'message' => 'Invalid record selected for deletion.'];
        }
    }
}

$departments =$pdo->query("SELECT * FROM departments ORDER BY dept_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments - HOPE SIMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="font-sans text-slate-800 min-h-full flex flex-col antialiased selection:bg-blue-900 selection:text-white"
      x-data="{
          editModalOpen: false,
          editData: { dept_id: '', dept_name: '' },
          openEditModal(dept) {
              this.editData = { dept_id: dept.dept_id, dept_name: dept.dept_name };
              this.editModalOpen = true;
          }
      }">

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
                        <span class="ml-1 text-slate-500">Academic Structure</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 font-semibold text-slate-800">Departments</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200/80">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-950 tracking-tight">Academic Departments</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-900 border border-blue-200/60">
                        Entity Manager
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Configure academic divisions and structural departments across faculty domains.</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m-4 4V7" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Add Department</h2>
                            <p class="text-xs text-slate-500">Create a new organizational department.</p>
                        </div>
                    </div>
                </div>

                <form action="manage_departments.php" method="POST" class="p-6 space-y-5">
                    <input type="hidden" name="action" value="create">

                    <!-- Department Name -->
                    <div>
                        <label for="dept_name" class="block text-sm font-medium text-slate-700 mb-1">
                            Department Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="dept_name" name="dept_name" required maxlength="100" placeholder="e.g. Computer Science"
                            class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm transition duration-150">
                        <p class="mt-1.5 text-xs text-slate-500">Official title of the academic division.</p>
                    </div>

                    <!-- Form Action Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full h-11 inline-flex items-center justify-center space-x-2 rounded-lg text-xs font-semibold text-white bg-blue-900 hover:bg-blue-800 active:bg-blue-950 transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Save Department</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column: Registered Departments Table -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Existing Departments</h2>
                        <p class="text-xs text-slate-500">Active structural divisions registered in database.</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        Total Departments: <?= count($departments); ?>
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-6">ID</th>
                                <th class="py-3 px-6">Department Title</th>
                                <th class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (!empty($departments)): ?>
                                <?php foreach ($departments as$d): ?>
                                    <tr class="hover:bg-slate-50/70 transition duration-150">
                                        <td class="py-3.5 px-6 font-mono text-xs text-slate-500">
                                            #<?= htmlspecialchars($d['dept_id']); ?>
                                        </td>
                                        <td class="py-3.5 px-6 font-semibold text-slate-900 flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-md bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-900 shrink-0 text-xs font-bold">
                                                <?= strtoupper(substr($d['dept_name'], 0, 2)); ?>
                                            </div>
                                            <span><?= htmlspecialchars($d['dept_name']); ?></span>
                                        </td>
                                        <td class="py-3.5 px-6 text-right space-x-1">
                                            <!-- Edit Button -->
                                            <button @click="openEditModal(<?= htmlspecialchars(json_encode($d)); ?>)"
                                                    class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition duration-150">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>

                                            <!-- Delete Button -->
                                            <button onclick="confirmDelete(<?= $d['dept_id']; ?>, '<?= addslashes(htmlspecialchars($d['dept_name'])); ?>')"
                                                    class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition duration-150">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="py-12 px-6 text-center text-slate-400">
                                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m-4 4V7" />
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No departments found.</p>
                                        <p class="text-xs text-slate-400 mt-1">Use the form on the left to add your first department.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- Hidden Form for Delete Actions -->
    <form id="delete-form" action="manage_departments.php" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="dept_id" id="delete_id">
    </form>

    <!-- Modal for Editing Department -->
    <div x-show="editModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="editModalOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="editModalOpen = false"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="editModalOpen" 
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-slate-200">
                
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900" id="modal-title">Edit Department</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="manage_departments.php" method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="dept_id" x-model="editData.dept_id">

                    <div>
                        <label for="edit_dept_name" class="block text-sm font-medium text-slate-700 mb-1">
                            Department Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit_dept_name" name="dept_name" required maxlength="100" x-model="editData.dept_name"
                            class="w-full h-11 px-4 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm">
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition duration-150">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2.5 rounded-lg text-xs font-semibold text-white bg-blue-900 hover:bg-blue-800 transition duration-150 shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <!-- SweetAlert Toast & Confirmation Scripts -->
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

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Delete Department?',
                text: `Are you sure you want to remove "${name}"? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete_id').value = id;
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
</body>

</html>