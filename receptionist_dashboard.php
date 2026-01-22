<?php
// receptionist_dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Receptionist') {
    header("Location: index.php");
    exit;
}
?>
<h1>Receptionist Dashboard</h1>
<p>Work in progress.</p>
<a href="logout.php">Logout</a>
