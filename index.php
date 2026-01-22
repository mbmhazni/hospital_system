<?php
// index.php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = :role");
        $stmt->execute(['email' => $email, 'role' => $role]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Redirect based on role
            switch ($user['role']) {
                case 'Admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'Doctor':
                    header("Location: doctor_dashboard.php");
                    break;
                case 'Receptionist':
                    header("Location: receptionist_dashboard.php");
                    break;
                case 'Patient':
                    header("Location: patient_portal.php");
                    break;
                default:
                    header("Location: index.php"); // Fallback
            }
            exit;
        } else {
            $error = "Invalid credentials or role mismatch.";
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Hospital System Login - MediCare Pro</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Manrope"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-[#111418] transition-colors duration-300">
<div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
    <!-- Header / Nav -->
    <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-[#dbe0e6] dark:border-[#2d3b48] bg-white dark:bg-background-dark px-6 md:px-20 py-3">
        <div class="flex items-center gap-3">
            <div class="text-primary">
                <svg class="size-8" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" d="M47.2426 24L24 47.2426L0.757355 24L24 0.757355L47.2426 24ZM12.2426 21H35.7574L24 9.24264L12.2426 21Z" fill="currentColor" fill-rule="evenodd"></path>
                </svg>
            </div>
            <h2 class="text-[#111418] dark:text-white text-xl font-extrabold leading-tight tracking-[-0.015em]">MediCare Pro</h2>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-[#e8f3ff] dark:bg-primary/20 rounded-full border border-primary/20">
                <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-xs font-bold text-primary tracking-wide">SYSTEM ONLINE</span>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <main class="flex flex-1 flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-[480px]">
            <!-- Headline Section -->
            <div class="mb-8">
                <h1 class="text-[#111418] dark:text-white tracking-tight text-[32px] font-extrabold leading-tight text-center">Welcome Back</h1>
                <p class="text-[#617589] dark:text-gray-400 text-base font-normal leading-normal text-center mt-2">Log in to manage your medical services and patients</p>
                <?php if($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Login Card -->
            <div class="bg-white dark:bg-[#1a2530] rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.3)] border border-[#f0f2f4] dark:border-[#2d3b48] overflow-hidden">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <!-- Role Selector Container -->
                <div class="p-6 pb-2">
                    <p class="text-[#111418] dark:text-white text-sm font-bold mb-3 px-1 uppercase tracking-wider">I am a:</p>
                    <div class="flex h-12 items-center justify-center rounded-lg bg-background-light dark:bg-background-dark/50 p-1">
                        <label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-lg px-2 has-[:checked]:bg-white dark:has-[:checked]:bg-[#2d3b48] has-[:checked]:shadow-[0_2px_4px_rgba(0,0,0,0.05)] has-[:checked]:text-primary text-[#617589] dark:text-gray-400 text-sm font-bold leading-normal transition-all">
                            <span class="truncate">Admin</span>
                            <input class="invisible w-0" name="role" type="radio" value="Admin" />
                        </label>
                        <label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-lg px-2 has-[:checked]:bg-white dark:has-[:checked]:bg-[#2d3b48] has-[:checked]:shadow-[0_2px_4px_rgba(0,0,0,0.05)] has-[:checked]:text-primary text-[#617589] dark:text-gray-400 text-sm font-bold leading-normal transition-all">
                            <span class="truncate">Doctor</span>
                            <input checked="" class="invisible w-0" name="role" type="radio" value="Doctor"/>
                        </label>
                        <label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-lg px-2 has-[:checked]:bg-white dark:has-[:checked]:bg-[#2d3b48] has-[:checked]:shadow-[0_2px_4px_rgba(0,0,0,0.05)] has-[:checked]:text-primary text-[#617589] dark:text-gray-400 text-sm font-bold leading-normal transition-all">
                            <span class="truncate">Staff</span>
                            <input class="invisible w-0" name="role" type="radio" value="Receptionist"/>
                        </label>
                        <label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-lg px-2 has-[:checked]:bg-white dark:has-[:checked]:bg-[#2d3b48] has-[:checked]:shadow-[0_2px_4px_rgba(0,0,0,0.05)] has-[:checked]:text-primary text-[#617589] dark:text-gray-400 text-sm font-bold leading-normal transition-all">
                            <span class="truncate">Patient</span>
                            <input class="invisible w-0" name="role" type="radio" value="Patient"/>
                        </label>
                    </div>
                </div>
                <!-- Form Fields -->
                <div class="p-6 space-y-5">
                    <div class="flex flex-col gap-2">
                        <label class="text-[#111418] dark:text-white text-sm font-bold px-1">Username or Email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#617589] text-[20px]">mail</span>
                            <input name="email" class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#dbe0e6] dark:border-[#2d3b48] bg-white dark:bg-[#101922] focus:border-primary h-14 placeholder:text-[#617589] dark:placeholder:text-gray-500 pl-12 pr-4 text-base font-normal" placeholder="admin@medicare.com" type="text"/>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between px-1">
                            <label class="text-[#111418] dark:text-white text-sm font-bold">Password</label>
                            <a class="text-primary text-xs font-bold hover:underline" href="#">Forgot Password?</a>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#617589] text-[20px]">lock</span>
                            <input name="password" class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#dbe0e6] dark:border-[#2d3b48] bg-white dark:bg-[#101922] focus:border-primary h-14 placeholder:text-[#617589] dark:placeholder:text-gray-500 pl-12 pr-12 text-base font-normal" placeholder="password" type="password"/>
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-[#617589] hover:text-primary transition-colors" type="button">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 px-1">
                        <input class="size-4 rounded border-[#dbe0e6] dark:border-[#2d3b48] text-primary focus:ring-primary dark:bg-[#101922]" id="remember" type="checkbox"/>
                        <label class="text-sm font-medium text-[#617589] dark:text-gray-400 select-none" for="remember">Remember me for 30 days</label>
                    </div>
                    <button class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 px-4 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-all shadow-md shadow-primary/20" type="submit">
                        <span class="truncate">Secure Login</span>
                    </button>
                    <!-- Register Link -->
                    <div class="text-center mt-4">
                        <p class="text-sm text-[#617589]">New User? <a href="register_patient.php" class="text-primary font-bold hover:underline">Register as Patient</a></p>
                    </div>
                </div>
                </form>
                <!-- Card Footer -->
                <div class="border-t border-[#f0f2f4] dark:border-[#2d3b48] p-4 bg-background-light/30 dark:bg-black/10 text-center">
                    <p class="text-[#617589] dark:text-gray-400 text-xs font-medium">
                        Need help? <a class="text-primary hover:underline" href="#">Contact System Support</a>
                    </p>
                </div>
            </div>
            <!-- External Info -->
            <div class="mt-8 flex items-center justify-center gap-6">
                <div class="flex items-center gap-2 text-[#617589] dark:text-gray-500">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    <span class="text-xs font-semibold">256-bit Encryption</span>
                </div>
                <div class="flex items-center gap-2 text-[#617589] dark:text-gray-500">
                    <span class="material-symbols-outlined text-[18px]">gpp_good</span>
                    <span class="text-xs font-semibold">HIPAA Compliant</span>
                </div>
            </div>
        </div>
    </main>
    <!-- Page Footer -->
    <footer class="flex flex-col items-center justify-center pb-8 px-10 text-center">
        <p class="text-[#617589] dark:text-gray-500 text-sm">© 2024 MediCare Pro Hospital Management Systems. All rights reserved.</p>
        <div class="flex gap-4 mt-2">
            <a class="text-[#617589] dark:text-gray-500 text-xs hover:text-primary underline" href="#">Privacy Policy</a>
            <a class="text-[#617589] dark:text-gray-500 text-xs hover:text-primary underline" href="#">Terms of Service</a>
        </div>
    </footer>
</div>
</body>
</html>
