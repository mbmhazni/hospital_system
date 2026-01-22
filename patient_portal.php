<?php
// patient_portal.php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: index.php");
    exit;
}

$patient = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $patient = $stmt->fetch();
} catch (PDOException $e) {
    // Handle error
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Patient Portal - MediCare Pro</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
<body class="bg-background-light dark:bg-background-dark font-display text-[#111418] dark:text-white antialiased">
<div class="flex min-h-screen">
    <!-- Side Nav -->
    <aside class="w-64 flex-shrink-0 bg-white dark:bg-background-dark border-r border-[#dbe0e6] dark:border-slate-800 flex flex-col justify-between py-6 px-4">
        <div class="flex flex-col gap-8">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-primary size-10 rounded-lg flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">health_and_safety</span>
                </div>
                <h2 class="text-lg font-extrabold tracking-tight">Health Portal</h2>
            </div>
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary">
                    <span class="material-symbols-outlined">dashboard</span>
                    <p class="text-sm font-semibold">Dashboard</p>
                </div>
            </div>
        </div>
        <a href="logout.php" class="flex w-full cursor-pointer items-center justify-center rounded-lg h-10 px-4 bg-gray-200 text-gray-700 text-sm font-bold tracking-wide hover:bg-gray-300">
            Logout
        </a>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0">
        <div class="p-8 overflow-y-auto">
            <div class="flex flex-wrap justify-between items-end gap-3 mb-8">
                <div class="flex flex-col gap-1">
                    <p class="text-[#111418] dark:text-white text-3xl font-extrabold tracking-tight">Welcome, <?php echo htmlspecialchars($patient['full_name'] ?? 'Patient'); ?></p>
                    <p class="text-[#617589] text-base font-normal">Here's a quick look at your health profile.</p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-background-dark rounded-xl border border-[#dbe0e6] dark:border-slate-800 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">My Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Full Name</p>
                        <p class="font-bold"><?php echo htmlspecialchars($patient['full_name'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Email</p>
                        <p class="font-bold"><?php echo htmlspecialchars($patient['email'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Phone</p>
                        <p class="font-bold"><?php echo htmlspecialchars($patient['phone'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Address</p>
                        <p class="font-bold"><?php echo htmlspecialchars($patient['address'] ?? '-'); ?></p>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($patient['allergies'])): ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                <h3 class="text-lg font-bold text-red-700 mb-2">⚠ Allergies</h3>
                <p class="text-red-600"><?php echo htmlspecialchars($patient['allergies']); ?></p>
            </div>
            <?php endif; ?>

        </div>
    </main>
</div>
</body>
</html>
