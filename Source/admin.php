<?php
session_start();
$version = "1.1.4";

if (!is_dir('users')) { mkdir('users', 0777, true); }
if (!file_exists('users.json')) { file_put_contents('users.json', json_encode([])); }

$usersFile = 'users.json';
$userData = json_decode(file_get_contents($usersFile), true) ?: [];
$step = $_GET['step'] ?? (empty($userData) ? 'welcome' : 'manage');

if (!empty($userData) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header("Location: login.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = empty($userData) ? 'admin' : ($_POST['role'] ?? 'user');
    $pass = $_POST['password'];
    $oldName = $_POST['old_username'] ?? '';

    if (!empty($oldName) && $oldName !== $name) {
        if (file_exists("users/$oldName.json")) rename("users/$oldName.json", "users/$name.json");
        $userData[$name] = $userData[$oldName]; unset($userData[$oldName]);
    }

    if (!isset($userData[$name])) $userData[$name] = [];
    $userData[$name]['email'] = $email;
    $userData[$name]['role'] = $role;
    if (!empty($pass)) $userData[$name]['password'] = password_hash($pass, PASSWORD_DEFAULT);

    file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
    
    if ($step === 'register' && empty($_SESSION['user'])) {
        header("Location: admin.php?step=success");
    } else {
        header("Location: admin.php");
    }
    exit;
}

if (isset($_GET['delete']) && $_GET['delete'] !== $_SESSION['user']) {
    $del = $_GET['delete'];
    if (file_exists("users/$del.json")) unlink("users/$del.json");
    unset($userData[$del]);
    file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
    header("Location: admin.php"); exit;
}

$editUser = null;
if (isset($_GET['edit']) && isset($userData[$_GET['edit']])) {
    $editUser = $userData[$_GET['edit']];
    $step = 'register';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Vlyx Admin Portal</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M30 50 L55 5 L55 45 L80 45 L45 95 L45 55 Z' fill='%2300ddff'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --bg: #1e1e26; --card: #2b2a33; --accent: #00ddff; --text: #fbfbfe; }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .panel { background: var(--card); padding: 40px; border-radius: 25px; width: 100%; max-width: 550px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid rgba(0, 221, 255, 0.1); }
        h2 { color: var(--accent); margin: 0 0 10px; }
        p { color: #aaa; font-size: 0.9rem; margin-bottom: 25px; }
        .btn-vlyx { display: block; background: var(--accent); color: #1e1e26; font-weight: bold; border: none; padding: 15px; border-radius: 12px; cursor: pointer; text-decoration: none; text-transform: uppercase; text-align: center; width: 100%; box-sizing: border-box; }
        input, select { padding: 14px; margin: 10px 0; border-radius: 10px; border: 1px solid #444; background: #1e1e26; color: white; width: 100%; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 15px 10px; border-bottom: 1px solid #333; }
        .badge { background: rgba(0, 221, 255, 0.1); color: var(--accent); padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>
    <div class="panel">
        <i class="fa-solid fa-bolt-lightning" style="font-size: 3rem; color: var(--accent); margin-bottom: 25px; display: block; text-align: center;"></i>
        <?php if ($step === 'welcome'): ?>
            <h2 style="text-align:center;">Welcome to Vlyx</h2>
            <p style="text-align:center;">Setup is required. This wizard will initialize the system and create your master administrator account.</p>
            <a href="?step=register" class="btn-vlyx">Start Setup</a>
        <?php elseif ($step === 'register'): ?>
            <h2><?= $editUser ? 'Edit User' : 'Register User' ?></h2>
            <p>Enter account details. Email is optional but required for recovery.</p>
            <form method="POST">
                <input type="hidden" name="old_username" value="<?= $_GET['edit'] ?? '' ?>">
                <input type="text" name="username" placeholder="Username" required value="<?= $_GET['edit'] ?? '' ?>">
                <input type="email" name="email" placeholder="Email Address (Optional)" value="<?= $editUser['email'] ?? '' ?>">
                <input type="password" name="password" placeholder="<?= $editUser ? 'New Password (Optional)' : 'Password' ?>" <?= $editUser ? '' : 'required' ?>>
                <?php if (!empty($userData)): ?>
                    <select name="role">
                        <option value="user" <?= (isset($editUser['role']) && $editUser['role'] === 'user') ? 'selected' : '' ?>>Standard User</option>
                        <option value="admin" <?= (isset($editUser['role']) && $editUser['role'] === 'admin') ? 'selected' : '' ?>>Administrator</option>
                    </select>
                <?php endif; ?>
                <button type="submit" name="save_user" class="btn-vlyx"><?= $editUser ? 'Update' : 'Register' ?></button>
            </form>
        <?php elseif ($step === 'manage'): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0;">User Management</h2>
                <a href="?step=register" class="badge">+ Add User</a>
            </div>
            <table>
                <?php foreach ($userData as $name => $info): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($name) ?></strong></td>
                    <td><span class="badge"><?= htmlspecialchars($info['role'] ?? 'user') ?></span></td>
                    <td style="text-align:right;">
                        <a href="?edit=<?= urlencode($name) ?>" style="color:var(--accent); margin-right:15px;"><i class="fa-solid fa-user-pen"></i></a>
                        <?php if ($name !== $_SESSION['user']): ?>
                            <a href="?delete=<?= urlencode($name) ?>" style="color:#ff4444;" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash-can"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <a href="index.php" class="btn-vlyx" style="margin-top:30px; background:transparent; border:1px solid #444; color:#aaa;">Back to Hub</a>
        <?php elseif ($step === 'success'): ?>
            <h2 style="text-align:center;">Account Created!</h2>
            <p style="text-align:center;">System initialized. Please log in manually to confirm your credentials.</p>
            <a href="login.php" class="btn-vlyx">Proceed to Login</a>
        <?php endif; ?>
    </div>
</body>
</html>