<?php
// doctor_dashboard.php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: index.php");
    exit;
}

$doctor = [];
$appointments = [];

try {
    // Get Doctor Profile
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $doctor = $stmt->fetch();

    if ($doctor) {
        // Get Today's Appointments
        $stmt = $pdo->prepare("
            SELECT a.*, p.full_name as patient_name, p.chronic_conditions 
            FROM appointments a 
            JOIN patients p ON a.patient_id = p.id 
            WHERE a.doctor_id = ? AND DATE(a.appointment_date) = CURDATE()
            ORDER BY a.appointment_date ASC
        ");
        $stmt->execute([$doctor['id']]);
        $appointments = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    // Handle error
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Doctor Dashboard - MedSync</title>
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
<body class="bg-background-light dark:bg-background-dark text-[#111418] dark:text-white min-h-screen">
<div class="flex flex-col h-screen">
    <!-- Header -->
    <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-[#f0f2f4] dark:border-slate-800 bg-white dark:bg-background-dark px-6 py-3 shrink-0">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-3 text-[#111418] dark:text-white">
                <div class="size-8 bg-primary rounded-lg flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-xl">clinical_notes</span>
                </div>
                <h2 class="text-lg font-bold leading-tight tracking-tight">MedSync Clinical</h2>
            </div>
            <nav class="flex items-center gap-6">
                <a class="text-[#111418] dark:text-white text-sm font-semibold" href="#">Dashboard</a>
                <a class="text-[#617589] dark:text-slate-400 text-sm font-medium hover:text-primary" href="#">Patients</a>
            </nav>
        </div>
        <div class="flex items-center gap-3 ml-2">
            <div class="text-right hidden lg:block">
                <p class="text-sm font-bold leading-none"><?php echo htmlspecialchars($doctor['full_name'] ?? 'Doctor'); ?></p>
                <p class="text-xs text-[#617589]"><?php echo htmlspecialchars($doctor['specialization'] ?? 'General'); ?></p>
            </div>
            <a href="logout.php" class="flex items-center justify-center rounded-lg size-10 bg-[#f0f2f4] dark:bg-slate-800 hover:bg-red-100 text-red-500">
                <span class="material-symbols-outlined text-xl">logout</span>
            </a>
        </div>
    </header>

    <main class="flex flex-1 overflow-hidden">
        <!-- Sidebar: Appointments -->
        <aside class="w-80 flex flex-col border-r border-[#f0f2f4] dark:border-slate-800 bg-white dark:bg-background-dark overflow-y-auto">
            <div class="p-4">
                <h3 class="text-sm font-bold text-[#111418] dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg text-primary">event_list</span> Today's Appointments
                </h3>
                <div class="space-y-3">
                    <?php foreach($appointments as $appt): ?>
                    <div class="p-3 border border-[#f0f2f4] dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer group">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-green-100 text-green-700 rounded-full uppercase"><?php echo $appt['status']; ?></span>
                            <span class="text-xs font-semibold text-slate-500"><?php echo date('h:i A', strtotime($appt['appointment_date'])); ?></span>
                        </div>
                        <h4 class="text-sm font-bold group-hover:text-primary"><?php echo htmlspecialchars($appt['patient_name']); ?></h4>
                        <p class="text-xs text-[#617589]"><?php echo htmlspecialchars($appt['reason'] ?? 'Checkup'); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($appointments)): ?>
                        <p class="text-sm text-gray-500 text-center py-4">No appointments today.</p>
                    <?php endif; ?>
                </div>
            </div>
        </aside>

        <!-- Main Content Placeholder -->
        <section class="flex-1 flex flex-col items-center justify-center text-gray-400">
            <span class="material-symbols-outlined text-6xl mb-4">patient_list</span>
            <p>Select an appointment to view patient details</p>
        </section>
    </main>
</div>
</body>
</html>
