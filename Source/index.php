<?php
session_start();
$version = "1.1.4";

// Auto-create directories if missing
if (!is_dir('users')) { mkdir('users', 0777, true); }
if (!file_exists('users.json')) { file_put_contents('users.json', json_encode([])); }

$usersFile = 'users.json';
$userData = json_decode(file_get_contents($usersFile), true) ?: [];

// Redirect if no users are registered
if (empty($userData)) { header("Location: admin.php"); exit; }
if (isset($_GET['logout'])) { session_destroy(); header("Location: login.php"); exit; }
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$user = $_SESSION['user'];
$role = $_SESSION['role'] ?? 'user';
$userLinksFile = "users/" . $user . ".json";

// Logic for adding/deleting links and profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_link'])) {
        $links = file_exists($userLinksFile) ? json_decode(file_get_contents($userLinksFile), true) : [];
        $links[] = ['name' => $_POST['name'], 'url' => $_POST['url']];
        file_put_contents($userLinksFile, json_encode($links));
    } elseif (isset($_POST['delete_link'])) {
        $links = json_decode(file_get_contents($userLinksFile), true);
        array_splice($links, (int)$_POST['index'], 1);
        file_put_contents($userLinksFile, json_encode($links));
    } elseif (isset($_POST['update_profile'])) {
        $newName = trim($_POST['new_username']);
        if ($newName !== $user) {
            if (file_exists($userLinksFile)) rename($userLinksFile, "users/$newName.json");
            $userData[$newName] = $userData[$user]; unset($userData[$user]);
            $_SESSION['user'] = $newName;
        }
        $userData[$_SESSION['user']]['email'] = trim($_POST['email']);
        if (!empty($_POST['password'])) {
            $userData[$_SESSION['user']]['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
    }
    header("Location: index.php"); exit;
}
$links = file_exists($userLinksFile) ? json_decode(file_get_contents($userLinksFile), true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vlyx Hub - <?= htmlspecialchars($_SESSION['user']) ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M30 50 L55 5 L55 45 L80 45 L45 95 L45 55 Z' fill='%2300ddff'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --bg: #1e1e26; --card: #2b2a33; --accent: #00ddff; --text: #fbfbfe; --dark-bar: #121217; }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; align-items: center; margin: 0; min-height: 100vh; }
        .logo-container { display: flex; align-items: center; margin: 60px 0; }
        .brand-logo { font-size: 3.5rem; color: var(--accent); margin-right: 20px; }
        .brand-text { font-size: 2.8rem; font-weight: bold; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 30px; width: 100%; max-width: 1000px; padding: 0 20px 140px; box-sizing: border-box; }
        .bookmark-wrapper { position: relative; text-align: center; background: var(--card); padding: 25px; border-radius: 22px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); transition: 0.3s; }
        .icon-box { width: 60px; height: 60px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.25); border-radius: 15px; }
        .icon-box img { width: 36px; height: 36px; object-fit: contain; }
        .del-btn { position: absolute; top: 12px; right: 12px; background: none; border: none; color: #ff4444; cursor: pointer; opacity: 0.3; }
        .admin-bar { position: fixed; bottom: 0; width: 100%; background: var(--dark-bar); border-top: 1px solid rgba(0, 221, 255, 0.3); z-index: 100; padding: 12px 30px; box-sizing: border-box; display: flex; justify-content: space-between; align-items: center; }
        .admin-form { display: flex; align-items: center; gap: 12px; }
        input { padding: 10px 15px; border-radius: 8px; border: 1px solid #333; background: #1e1e26; color: white; box-sizing: border-box; }
        .nav-link { color: var(--accent); text-decoration: none; font-weight: bold; margin-left: 15px; cursor: pointer; font-size: 0.9rem; }
        .modal-overlay { display: none; position: fixed; z-index: 200; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px); }
        .modal-content { background: var(--card); margin: 10vh auto; padding: 40px; border-radius: 25px; width: 90%; max-width: 480px; border: 1px solid rgba(0, 221, 255, 0.4); position: relative; animation: slideUp 0.3s ease-out; }
    </style>
</head>
<body>
    <div class="logo-container">
        <div class="brand-logo"><i class="fa-solid fa-bolt-lightning"></i></div>
        <div class="brand-text"><span><?= htmlspecialchars($_SESSION['user']) ?>'s</span> Vlyx</div>
    </div>
    <div class="admin-bar">
        <div class="bar-info">Vlyx Hub v<?= $version ?> &bull; GitHub<br><span style="color: #333;">System is up to date</span></div>
    </div>
</body>
</html>