<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { die("Unauthorized access."); }

$usersFile = 'users.json';
$userData = json_decode(file_get_contents($usersFile), true) ?: [];
$editMode = false; $targetUser = '';

if (isset($_GET['edit'])) { $editMode = true; $targetUser = $_GET['edit']; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $pass = $_POST['password'];
    $oldName = $_POST['old_username'] ?? '';

    if ($editMode && $oldName !== $name) {
        if (file_exists("users/$oldName.json")) rename("users/$oldName.json", "users/$name.json");
        $userData[$name] = $userData[$oldName]; unset($userData[$oldName]);
    }

    if (!isset($userData[$name])) $userData[$name] = [];
    $userData[$name]['email'] = $email;
    $userData[$name]['role'] = $role;
    if (!empty($pass)) $userData[$name]['password'] = password_hash($pass, PASSWORD_DEFAULT);

    file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
    header("Location: admin.php"); exit;
}

if (isset($_GET['delete']) && $_GET['delete'] !== $_SESSION['user']) {
    $del = $_GET['delete'];
    if (file_exists("users/$del.json")) unlink("users/$del.json");
    unset($userData[$del]);
    file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
    header("Location: admin.php"); exit;
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
        body { background: #1e1e26; color: white; font-family: sans-serif; padding: 40px; display: flex; flex-direction: column; align-items: center; }
        .panel { background: #2b2a33; padding: 30px; border-radius: 20px; width: 100%; max-width: 800px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h2 { color: #00ddff; margin-bottom: 25px; }
        input, select { padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #444; background: #1e1e26; color: white; width: 100%; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 15px; border-bottom: 1px solid #444; text-align: left; }
        .btn { background: #00ddff; color: #1e1e26; font-weight: bold; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="panel">
        <h2><i class="fa-solid fa-shield-halved"></i> Admin Management</h2>
        <form method="POST">
            <input type="hidden" name="old_username" value="<?= htmlspecialchars($targetUser) ?>">
            <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($targetUser) ?>">
            <input type="email" name="email" placeholder="Email" value="<?= $editMode ? htmlspecialchars($userData[$targetUser]['email'] ?? '') : '' ?>">
            <input type="password" name="password" placeholder="<?= $editMode ? 'New Password (Optional)' : 'Password' ?>" <?= $editMode ? '' : 'required' ?>>
            <select name="role">
                <option value="user" <?= $editMode && ($userData[$targetUser]['role'] ?? '') === 'user' ? 'selected' : '' ?>>Standard User</option>
                <option value="admin" <?= $editMode && ($userData[$targetUser]['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
            </select>
            <button type="submit" name="save_user" class="btn"><?= $editMode ? 'Update User' : 'Create User' ?></button>
        </form>
        <table>
            <tr><th>Name</th><th>Role</th><th>Actions</th></tr>
            <?php foreach($userData as $name => $info): ?>
            <tr>
                <td><?= htmlspecialchars($name) ?></td>
                <td><?= htmlspecialchars($info['role'] ?? 'user') ?></td>
                <td>
                    <a href="?edit=<?= urlencode($name) ?>" style="color:#00ddff; margin-right:15px;"><i class="fa-solid fa-edit"></i></a>
                    <?php if($name !== $_SESSION['user']): ?>
                        <a href="?delete=<?= urlencode($name) ?>" style="color:#ff4444;" onclick="return confirm('Delete user?')"><i class="fa-solid fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="index.php" style="display:block; margin-top:25px; color:#888; text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</body>
</html>