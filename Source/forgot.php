<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $users = json_decode(file_get_contents('users.json'), true) ?: [];
    foreach($users as $user => $info) {
        if (isset($info['email']) && strtolower($info['email']) === strtolower($email)) {
            $token = bin2hex(random_bytes(32));
            $tokens = json_decode(file_get_contents('tokens.json'), true) ?: [];
            $tokens[$token] = ['user' => $user, 'expires' => time() + 3600];
            file_put_contents('tokens.json', json_encode($tokens));
            $msg = "Recovery link: reset.php?token=$token"; // For development display
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Reset Password - Vlyx</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: #1e1e26; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #2b2a33; padding: 40px; border-radius: 20px; width: 340px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 15px 0; border-radius: 8px; border: none; background: #1e1e26; color: white; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #00ddff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; color: #1e1e26; }
    </style>
</head>
<body>
    <div class="box">
        <i class="fa-solid fa-key" style="font-size:3rem; color:#00ddff; margin-bottom:20px;"></i>
        <h2>Reset Access</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter account email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <?php if(isset($msg)) echo "<p style='color:#00ddff; font-size:0.8rem; margin-top:20px;'>$msg</p>"; ?>
        <a href="login.php" style="color:#666; display:block; margin-top:20px; text-decoration:none;">Back to Login</a>
    </div>
</body>
</html>