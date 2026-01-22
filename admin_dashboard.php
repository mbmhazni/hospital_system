<?php
// admin_dashboard.php
session_start();
require_once 'db_connect.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

// Fetch Stats
$stats = [
    'patients' => 0,
    'doctors' => 0,
    'appointments' => 0,
    'revenue' => 0 // Mocked for now or sum from billing
];

try {
    $stats['patients'] = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $stats['doctors'] = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    $stats['appointments'] = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    
    // Fetch Recent Doctors
    $doctorsStmt = $pdo->query("SELECT * FROM doctors ORDER BY id DESC LIMIT 5");
    $recentDoctors = $doctorsStmt->fetchAll();

} catch (PDOException $e) {
    // Handle error
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Admin Dashboard - MediCare Pro</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {"primary": "#137fec", "background-light": "#f6f7f8", "background-dark": "#101922"},
                    fontFamily: {"display": ["Manrope", "sans-serif"]},
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark text-[#111418] dark:text-white antialiased">
<div class="flex h-screen overflow-hidden">
    <aside class="w-64 flex-shrink-0 border-r border-[#dbe0e6] dark:border-gray-800 bg-white dark:bg-background-dark hidden lg:flex flex-col">
        <div class="p-6">
            <div class="flex items-center gap-3 text-primary mb-10">
                <span class="material-symbols-outlined text-3xl font-bold">local_hospital</span>
                <h2 class="text-xl font-extrabold tracking-tight">MedSystem</h2>
            </div>
            <nav class="flex flex-col gap-1.5">
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary text-white transition-all" href="#">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span class="text-sm font-semibold">Dashboard</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all" href="#">
                    <span class="material-symbols-outlined">group</span>
                    <span class="text-sm font-semibold">Patients</span>
                </a>
                <!-- More links... -->
            </nav>
        </div>
        <div class="mt-auto p-6 border-t border-[#dbe0e6] dark:border-gray-800">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gray-300"></div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold">Admin User</p>
                    <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                </div>
            </div>
            <a href="logout.php" class="flex items-center gap-3 px-4 py-2 w-full rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all">
                <span class="material-symbols-outlined text-xl">logout</span>
                <span class="text-sm font-semibold">Logout</span>
            </a>
        </div>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-16 flex items-center justify-between px-8 bg-white dark:bg-background-dark border-b border-[#dbe0e6] dark:border-gray-800 sticky top-0 z-10">
            <h1 class="text-xl font-bold">Dashboard Overview</h1>
        </header>

        <div class="p-8 space-y-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 bg-white dark:bg-background-dark border border-[#dbe0e6] dark:border-gray-800 rounded-xl flex flex-col gap-2">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Patients</p>
                    <p class="text-3xl font-extrabold"><?php echo $stats['patients']; ?></p>
                </div>
                <div class="p-6 bg-white dark:bg-background-dark border border-[#dbe0e6] dark:border-gray-800 rounded-xl flex flex-col gap-2">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Doctors</p>
                    <p class="text-3xl font-extrabold"><?php echo $stats['doctors']; ?></p>
                </div>
                <div class="p-6 bg-white dark:bg-background-dark border border-[#dbe0e6] dark:border-gray-800 rounded-xl flex flex-col gap-2">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Appointments</p>
                    <p class="text-3xl font-extrabold"><?php echo $stats['appointments']; ?></p>
                </div>
            </div>

            <!-- Doctors Table -->
            <div class="bg-white dark:bg-background-dark border border-[#dbe0e6] dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-[#dbe0e6] dark:border-gray-800">
                    <h2 class="text-lg font-bold">Recent Doctors</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-background-light dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Specialization</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#dbe0e6] dark:divide-gray-800">
                            <?php foreach($recentDoctors as $doc): ?>
                            <tr>
                                <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($doc['full_name']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($doc['specialization']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700"><?php echo htmlspecialchars($doc['status']); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($recentDoctors)): ?>
                                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No doctors found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
