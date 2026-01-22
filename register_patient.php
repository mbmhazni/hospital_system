<?php
// register_patient.php
session_start();
require_once 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic Validation
    if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['full_name'])) {
        $error = "Required fields are missing.";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Create User
            $email = trim($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = 'Patient';

            $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$email, $password, $role]);
            $user_id = $pdo->lastInsertId();

            // 2. Create Patient Record
            $full_name = $_POST['full_name'];
            $dob = $_POST['dob'];
            $gender = $_POST['gender'];
            $occupation = $_POST['occupation'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $ec_name = $_POST['emergency_contact_name'] ?? '';
            $ec_rel = $_POST['emergency_contact_rel'] ?? '';
            
            // Handle Checkboxes (Chronic Conditions)
            $conditions = isset($_POST['conditions']) ? json_encode($_POST['conditions']) : json_encode([]);
            
            $allergies = $_POST['allergies'] ?? '';
            $meds = $_POST['medications'] ?? '';
            $smoking = $_POST['smoking'] ?? '';
            $alcohol = $_POST['alcohol'] ?? '';

            $sql = "INSERT INTO patients (user_id, full_name, date_of_birth, gender, occupation, phone, email, address, 
                    emergency_contact_name, emergency_contact_rel, chronic_conditions, allergies, current_medications, 
                    smoking_status, alcohol_status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $full_name, $dob, $gender, $occupation, $phone, $email, $address, 
                            $ec_name, $ec_rel, $conditions, $allergies, $meds, $smoking, $alcohol]);

            $pdo->commit();
            
            // Auto-login or redirect
            $success = "Registration successful! You can now login.";
            header("refresh:2;url=index.php");
            
        } catch (Exception $e) {
            $pdo->rollBack();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $error = "Email already exists.";
            } else {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Patient Registration - MediCare Pro</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet"/>
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
<body class="bg-background-light dark:bg-background-dark min-h-screen font-display">
<header class="bg-white dark:bg-background-dark border-b border-[#dbe0e6] dark:border-gray-800 sticky top-0 z-50">
    <div class="max-w-[1200px] mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="bg-primary p-1.5 rounded-lg text-white">
                <span class="material-symbols-outlined block">local_hospital</span>
            </div>
            <h2 class="text-[#111418] dark:text-white text-lg font-bold leading-tight tracking-tight">MediCare Pro</h2>
        </div>
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-sm font-bold text-primary hover:underline">Back to Login</a>
        </div>
    </div>
</header>
<main class="max-w-[960px] mx-auto py-10 px-6">
    <div class="mb-8">
        <h1 class="text-[#111418] dark:text-white text-4xl font-black leading-tight tracking-tight mb-2">New Patient Registration</h1>
        <p class="text-[#617589] dark:text-gray-400 text-lg">Please accurately enter your legal and medical information.</p>
        
        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
    </div>

    <form class="space-y-8" method="POST" action="">
        <!-- Section 1: Personal Information -->
        <div class="bg-white dark:bg-background-dark/50 border border-[#dbe0e6] dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-[#dbe0e6] dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30">
                <h2 class="text-[#111418] dark:text-white text-xl font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">person</span> Personal Information
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <label class="flex flex-col gap-2">
                    <span class="text-[#111418] dark:text-white text-sm font-semibold">Full Legal Name *</span>
                    <input required name="full_name" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary transition-all px-4" placeholder="e.g. Johnathan Doe" type="text"/>
                </label>
                <label class="flex flex-col gap-2">
                    <span class="text-[#111418] dark:text-white text-sm font-semibold">Date of Birth *</span>
                    <input required name="dob" class="w-full h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4" type="date"/>
                </label>
                <label class="flex flex-col gap-2">
                    <span class="text-[#111418] dark:text-white text-sm font-semibold">Gender Identity *</span>
                    <select required name="gender" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Non-binary">Non-binary</option>
                        <option value="Prefer not to say">Prefer not to say</option>
                    </select>
                </label>
                <label class="flex flex-col gap-2">
                    <span class="text-[#111418] dark:text-white text-sm font-semibold">Occupation</span>
                    <input name="occupation" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4" placeholder="e.g. Software Engineer" type="text"/>
                </label>
            </div>
        </div>

        <!-- Section 2: Contact Information -->
        <div class="bg-white dark:bg-background-dark/50 border border-[#dbe0e6] dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-[#dbe0e6] dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30">
                <h2 class="text-[#111418] dark:text-white text-xl font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">contact_phone</span> Contact Details
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Added Password Field here for Account Creation -->
                <div class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-lg border border-blue-100 dark:border-blue-900/30">
                    <h3 class="text-sm font-bold text-primary mb-2">Account Security</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <label class="flex flex-col gap-2">
                            <span class="text-[#111418] dark:text-white text-sm font-semibold">Email Address *</span>
                            <input required name="email" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4" placeholder="john.doe@example.com" type="email"/>
                        </label>
                        <label class="flex flex-col gap-2">
                            <span class="text-[#111418] dark:text-white text-sm font-semibold">Create Password *</span>
                            <input required name="password" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4" type="password" placeholder="••••••••"/>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="flex flex-col gap-2">
                        <span class="text-[#111418] dark:text-white text-sm font-semibold">Phone Number</span>
                        <input name="phone" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4" placeholder="+1 (555) 000-0000" type="tel"/>
                    </label>
                    <label class="flex flex-col gap-2">
                        <span class="text-[#111418] dark:text-white text-sm font-semibold">Residential Address</span>
                        <input name="address" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4" placeholder="Street Address, City, State, ZIP" type="text"/>
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-dashed border-[#dbe0e6] dark:border-gray-800">
                    <label class="flex flex-col gap-2">
                        <span class="text-[#111418] dark:text-white text-sm font-semibold">Emergency Contact Name</span>
                        <input name="emergency_contact_name" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4" placeholder="Name" type="text"/>
                    </label>
                    <label class="flex flex-col gap-2">
                        <span class="text-[#111418] dark:text-white text-sm font-semibold">Relationship</span>
                        <input name="emergency_contact_rel" class="h-12 rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary px-4" placeholder="e.g. Spouse" type="text"/>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 3: Medical History -->
        <div class="bg-white dark:bg-background-dark/50 border border-[#dbe0e6] dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-[#dbe0e6] dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30">
                <h2 class="text-[#111418] dark:text-white text-xl font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">clinical_notes</span> Medical History
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <span class="text-[#111418] dark:text-white text-sm font-semibold block mb-4">Known Chronic Conditions</span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input name="conditions[]" value="Diabetes" class="size-5 rounded border-[#dbe0e6] text-primary focus:ring-primary" type="checkbox"/>
                            <span class="text-sm text-[#111418] dark:text-gray-300">Diabetes</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input name="conditions[]" value="Hypertension" class="size-5 rounded border-[#dbe0e6] text-primary focus:ring-primary" type="checkbox"/>
                            <span class="text-sm text-[#111418] dark:text-gray-300">Hypertension</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input name="conditions[]" value="Asthma" class="size-5 rounded border-[#dbe0e6] text-primary focus:ring-primary" type="checkbox"/>
                            <span class="text-sm text-[#111418] dark:text-gray-300">Asthma</span>
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <label class="flex flex-col gap-2">
                        <span class="text-[#111418] dark:text-white text-sm font-semibold">Known Allergies</span>
                        <textarea name="allergies" class="rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary p-4" rows="2"></textarea>
                    </label>
                    <label class="flex flex-col gap-2">
                        <span class="text-[#111418] dark:text-white text-sm font-semibold">Current Medications</span>
                        <textarea name="medications" class="rounded-lg border-[#dbe0e6] dark:border-gray-700 dark:bg-background-dark dark:text-white focus:ring-primary focus:border-primary p-4" rows="2"></textarea>
                    </label>
                </div>
                <div class="flex flex-wrap gap-10 py-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-[#111418] dark:text-white text-sm font-semibold">Smoking Status</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2"><input class="text-primary" name="smoking" value="Smoker" type="radio"/><span class="text-sm">Smoker</span></label>
                            <label class="flex items-center gap-2"><input class="text-primary" name="smoking" value="Non-Smoker" type="radio"/><span class="text-sm">Non-Smoker</span></label>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <span class="text-[#111418] dark:text-white text-sm font-semibold">Alcohol Consumption</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2"><input class="text-primary" name="alcohol" value="Regular" type="radio"/><span class="text-sm">Regular</span></label>
                            <label class="flex items-center gap-2"><input class="text-primary" name="alcohol" value="Occasional" type="radio"/><span class="text-sm">Occasional</span></label>
                            <label class="flex items-center gap-2"><input class="text-primary" name="alcohol" value="Never" type="radio"/><span class="text-sm">Never</span></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-end gap-4 py-8 border-t border-[#dbe0e6] dark:border-gray-800">
            <button class="w-full sm:w-auto px-10 h-14 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all flex items-center justify-center gap-2" type="submit">
                Complete Registration
                <span class="material-symbols-outlined">check_circle</span>
            </button>
        </div>
    </form>
</main>
</body>
</html>
