<?php
session_start();
$version = "1.1.5";

// --- AUTO-INSTALLER & VERSION FILE GENERATION ---
if (!is_dir('users')) { mkdir('users', 0777, true); }
if (!file_exists('users.json')) { file_put_contents('users.json', json_encode([])); }
if (!file_exists('version.txt')) { file_put_contents('version.txt', $version); }

$usersFile = 'users.json';
$userData = json_decode(file_get_contents($usersFile), true) ?: [];
$step = $_GET['step'] ?? (empty($userData) ? 'welcome' : 'manage');

// Security check
if (!empty($userData) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header("Location: login.php"); exit;
}
// ... (Rest of User Management and Registration logic from v1.1.4) ...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Vlyx Admin Portal</title>
    </head>
<body>
    <div class="panel">
        <i class="fa-solid fa-bolt-lightning" style="font-size: 3rem; color: var(--accent); margin-bottom: 25px; display: block; text-align: center;"></i>
        </div>
    <div style="position:fixed; bottom:20px; right:20px; color:#444; font-size:0.8rem; font-weight:bold;">Vlyx v<?= $version ?></div>
</body>
</html>