<?php
session_start();

// --- AUTO-CREATE DIRECTORIES AND FILES ---
if (!is_dir('users')) { mkdir('users', 0777, true); }
if (!file_exists('users.json')) { file_put_contents('users.json', json_encode([])); }
if (!file_exists('tokens.json')) { file_put_contents('tokens.json', json_encode([])); }

$usersFile = 'users.json';
$userData = json_decode(file_get_contents($usersFile), true) ?: [];

// Only check for session if at least one user exists
if (!empty($userData)) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die("Unauthorized access.");
    }
}

$editMode = false; $targetUser = '';
if (isset($_GET['edit'])) { $editMode = true; $targetUser = $_GET['edit']; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $pass = $_POST['password'];
    $oldName = $_POST['old_username'] ?? '';

    // LOGIC: If this is the FIRST user, force Admin role
    if (empty($userData)) {
        $role = 'admin';
    }

    if ($editMode && $oldName !== $name) {
        if (file_exists("users/$oldName.json")) rename("users/$oldName.json", "users/$name.json");
        $userData[$name] = $userData[$oldName]; unset($userData[$oldName]);
    }

    if (!isset($userData[$name])) $userData[$name] = [];
    $userData[$name]['email'] = $email;
    $userData[$name]['role'] = $role;
    if (!empty($pass)) $userData[$name]['password'] = password_hash($pass, PASSWORD_DEFAULT);

    file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
    
    // Redirect to login after first user setup
    if (empty($_SESSION['user'])) {
        header("Location: login.php");
    } else {
        header("Location: admin.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vlyx Administration</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M30 50 L55 5 L55 45 L80 45 L45 95 L45 55 Z' fill='%2300ddff'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --bg: #1e1e26; --card: #2b2a33; --accent: #00ddff; --text: #fbfbfe; }
        body { background: var(--bg); color: var(--text); font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .panel { background: var(--card); padding: 40px; border-radius: 25px; width: 100%; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); text-align: center; border: 1px solid rgba(0, 221, 255, 0.2); }
        h2 { color: var(--accent); margin-bottom: 10px; }
        p { color: #888; margin-bottom: 30px; font-size: 0.9rem; }
        input, select { padding: 14px; margin: 10px 0; border-radius: 10px; border: 1px solid #444; background: #1e1e26; color: white; width: 100%; box-sizing: border-box; }
        .btn-setup { background: var(--accent); color: #1e1e26; font-weight: bold; border: none; padding: 16px; border-radius: 10px; cursor: pointer; width: 100%; margin-top: 15px; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="panel">
        <i class="fa-solid fa-bolt-lightning" style="font-size: 3rem; color: var(--accent); margin-bottom: 20px;"></i>
        <h2><?= empty($userData) ? 'System Setup' : 'User Management' ?></h2>
        <p><?= empty($userData) ? 'Create the first administrator account to start.' : 'Manage system accounts and roles.' ?></p>
        
        <form method="POST">
            <input type="hidden" name="old_username" value="<?= htmlspecialchars($targetUser) ?>">
            <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($targetUser) ?>">
            <input type="email" name="email" placeholder="Email Address" value="<?= $editMode ? htmlspecialchars($userData[$targetUser]['email'] ?? '') : '' ?>">
            <input type="password" name="password" placeholder="Password" <?= $editMode ? '' : 'required' ?>>
            
            <?php if (!empty($userData)): ?>
            <select name="role">
                <option value="user" <?= $editMode && ($userData[$targetUser]['role'] ?? '') === 'user' ? 'selected' : '' ?>>Standard User</option>
                <option value="admin" <?= $editMode && ($userData[$targetUser]['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
            </select>
            <?php else: ?>
                <input type="hidden" name="role" value="admin">
                <div style="color: var(--accent); font-size: 0.8rem; margin-bottom: 10px;">First user will be granted Admin privileges.</div>
            <?php endif; ?>

            <button type="submit" name="save_user" class="btn-setup"><?= empty($userData) ? 'Initialize System' : 'Save User' ?></button>
        </form>
        
        <?php if (!empty($_SESSION['user'])): ?>
            <a href="index.php" style="display:block; margin-top:25px; color:#888; text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>