<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { die("Access denied."); }

$usersFile = 'users.json';
if (!file_exists($usersFile)) { file_put_contents($usersFile, json_encode([])); }
$userData = json_decode(file_get_contents($usersFile), true);
if (!is_array($userData)) { $userData = []; }

$editMode = false;
$oldName = '';
if (isset($_GET['edit'])) { $editMode = true; $oldName = $_GET['edit']; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $newName = trim($_POST['target_user']);
    $email = trim($_POST['target_email']);
    $password = $_POST['target_pass'];
    $originalName = $_POST['original_name'] ?? '';

    if (!empty($newName)) {
        $nameConflict = false;
        foreach ($userData as $existingName => $info) {
            if (strtolower($existingName) === strtolower($newName) && $existingName !== $originalName) {
                $nameConflict = true; break;
            }
        }
        if ($nameConflict) { $error = "Username already exists."; } else {
            if ($editMode && !empty($originalName) && $newName !== $originalName) {
                $oldF = "users/$originalName.json"; $newF = "users/$newName.json";
                if (file_exists($oldF)) { rename($oldF, $newF); }
                $userData[$newName] = $userData[$originalName]; unset($userData[$originalName]);
                if ($originalName === $_SESSION['user']) { $_SESSION['user'] = $newName; }
            }
            if (!isset($userData[$newName])) { $userData[$newName] = ["role" => "user"]; }
            $userData[$newName]['email'] = $email;
            if (!empty($password)) { $userData[$newName]['password'] = password_hash($password, PASSWORD_DEFAULT); }
            file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
            header("Location: admin.php?success=1"); exit;
        }
    }
}
// Verwijderen logic...
if (isset($_GET['delete']) && $_GET['delete'] !== $_SESSION['user']) {
    unset($userData[$_GET['delete']]);
    file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
    header("Location: admin.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vlyx Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #2b2a33; color: white; font-family: sans-serif; padding: 40px; }
        .box { background: #42414d; padding: 30px; border-radius: 12px; max-width: 900px; margin: 0 auto; }
        .form-group { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        input { padding: 12px; border-radius: 6px; border: none; background: #2b2a33; color: white; width: 180px; }
        .pass-wrapper { position: relative; }
        .eye-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #aaa; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #555; text-align: left; }
        .back-link { color: #00ddff; text-decoration: none; font-weight: bold; display: block; margin-top: 25px; }
    </style>
</head>
<body>
    <div class="box">
        <h2><i class="fas fa-shield-halved"></i> User Management</h2>
        <form method="POST" class="form-group">
            <input type="hidden" name="original_name" value="<?= htmlspecialchars($oldName) ?>">
            <input type="text" name="target_user" placeholder="Username" required value="<?= htmlspecialchars($oldName) ?>">
            <input type="email" name="target_email" placeholder="Email (Optional)" value="<?= $editMode ? htmlspecialchars($userData[$oldName]['email'] ?? '') : '' ?>">
            <div class="pass-wrapper">
                <input type="password" id="p_field" name="target_pass" placeholder="Password" <?= $editMode ? '' : 'required' ?>>
                <button type="button" class="eye-btn" onclick="togg()"><i id="eye" class="fas fa-eye"></i></button>
            </div>
            <button type="submit" name="save_user" style="background:#00ddff; border:none; border-radius:6px; padding:12px; font-weight:bold; cursor:pointer;">Save</button>
        </form>
        <table>
            <tr><th>Username</th><th>Email</th><th>Actions</th></tr>
            <?php foreach ($userData as $name => $info): ?>
            <tr>
                <td><?= htmlspecialchars($name) ?></td>
                <td><?= htmlspecialchars($info['email'] ?? '-') ?></td>
                <td>
                    <a href="?edit=<?= urlencode($name) ?>" style="color:#00ddff;"><i class="fas fa-edit"></i></a>
                    <?php if($name !== $_SESSION['user']): ?>
                        <a href="?delete=<?= urlencode($name) ?>" style="color:#ff4444; margin-left:10px;"><i class="fas fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <script>
        function togg() {
            const f = document.getElementById('p_field'), e = document.getElementById('eye');
            if (f.type === 'password') { f.type = 'text'; e.className = 'fas fa-eye-slash'; }
            else { f.type = 'password'; e.className = 'fas fa-eye'; }
        }
    </script>
</body>
</html>