<?php
// setup_database.php
$host = 'localhost';
$username = 'root';
$password = ''; // Check if you have a password set in XAMPP
$dbname = 'hospital_system';

try {
    // 1. Connect without Database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.<br>";

    // 2. Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "Database '$dbname' checked/created successfully.<br>";

    // 3. Select Database
    $pdo->exec("USE `$dbname`");

    // 4. Read and Execute SQL file
    $sqlFile = 'database.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Split SQL by semicolon to execute statements individually is safer, 
        // but for simple dumps, PDO sometimes handles multiple queries if emulation is on.
        // Let's try executing full script.
        $pdo->exec($sql);
        echo "Database schema and seed data imported successfully from $sqlFile.<br>";
        echo "<strong style='color:green'>Setup Complete!</strong> You can now <a href='index.php'>Login here</a>.";
    } else {
        echo "<strong style='color:red'>Error:</strong> database.sql file not found in the same directory.";
    }

} catch (PDOException $e) {
    echo "<strong style='color:red'>Setup Failed:</strong> " . $e->getMessage() . "<br>";
    echo "If access is denied, checking your db_connect.php password setting might be needed.";
}
?>
