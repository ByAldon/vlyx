<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$usersFile = 'users.json';
$userData = json_decode(file_get_contents($usersFile), true);
$oldName = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['username']);
    $newEmail = trim($_POST['email']);
    $newPass = $_POST['password'];
    $error = null;

    if (strtolower($newName) !== strtolower($oldName)) {
        foreach ($userData as $name => $info) {
            if (strtolower($name) === strtolower($newName)) { $error = "Username taken."; break; }
        }
    }
    if (!$error && !empty($newName)) {
        if ($newName !== $oldName) {
            $oldF = "users/$oldName.json"; $newF = "users/$newName.json";
            if (file_exists($oldF)) { rename($oldF, $newF); }
            $userData[$newName] = $userData[$oldName]; unset($userData[$oldName]);
            $_SESSION['user'] = $newName;
        }
        $userData[$newName]['email'] = $newEmail;
        if (!empty($newPass)) { $userData[$newName]['password'] = password_hash($newPass, PASSWORD_DEFAULT); }
        file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
        $success = "Saved!"; $oldName = $newName;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #2b2a33; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #42414d; padding: 35px; border-radius: 15px; width: 400px; }
        input { width: 100%; padding: 12px; margin: 8px 0; border-radius: 6px; border: none; background: #2b2a33; color: white; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #00ddff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; color: #2b2a33; margin-top: 20px; }
        .back { display: block; margin-top: 20px; color: #00ddff; text-decoration: none; text-align: center; }
    </style>
</head>
<body>
    <div class="box">
        <h2><i class="fas fa-user-gear"></i> Settings</h2>
        <?php if(isset($success)) echo "<p style='color:#00ff00;'>$success</p>"; ?>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($oldName) ?>" required>
            <label>Email (Optional):</label>
            <input type="email" name="email" value="<?= htmlspecialchars($userData[$oldName]['email'] ?? '') ?>">
            <label>New Password:</label>
            <input type="password" name="password" placeholder="Leave blank to keep">
            <button type="submit">Save Changes</button>
        </form>
        <a href="index.php" class="back"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</body>
</html>