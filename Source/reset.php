<?php
$tokensFile = 'tokens.json';
$usersFile = 'users.json';
$token = $_GET['token'] ?? '';
$tokens = file_exists($tokensFile) ? json_decode(file_get_contents($tokensFile), true) : [];

if (!isset($tokens[$token]) || $tokens[$token]['expires'] < time()) {
    die("Invalid or expired token. Please request a new reset link.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['password'];
    $username = $tokens[$token]['user'];
    $userData = json_decode(file_get_contents($usersFile), true);

    // Update password
    $userData[$username]['password'] = password_hash($newPass, PASSWORD_DEFAULT);
    file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));

    // Remove used token
    unset($tokens[$token]);
    file_put_contents($tokensFile, json_encode($tokens, JSON_PRETTY_PRINT));

    $success = "Password reset successful! You can now log in.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Password - My Space</title>
    <style>
        body { background: #2b2a33; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #42414d; padding: 40px; border-radius: 15px; width: 340px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 15px 0; border-radius: 6px; border: none; background: #2b2a33; color: white; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #00ddff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; color: #2b2a33; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Set New Password</h2>
        <?php if(isset($success)): ?>
            <p style="color:#00ff00;"><?= $success ?></p>
            <a href="login.php" style="color:#00ddff; text-decoration:none;">Go to Login</a>
        <?php else: ?>
            <form method="POST">
                <input type="password" name="password" placeholder="New Password" required minlength="6">
                <button type="submit">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>