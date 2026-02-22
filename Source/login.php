<?php
session_start();
if (isset($_SESSION['user'])) { header("Location: index.php"); exit; }
$usersFile = 'users.json';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUser = trim($_POST['user'] ?? '');
    $inputPass = $_POST['pass'] ?? '';
    $userData = json_decode(file_get_contents($usersFile), true) ?: [];
    foreach ($userData as $username => $info) {
        if ((strtolower($inputUser) === strtolower($username) || (isset($info['email']) && strtolower($inputUser) === strtolower($info['email']))) && password_verify($inputPass, $info['password'])) {
            $_SESSION['user'] = $username; $_SESSION['role'] = $info['role'] ?? 'user'; header("Location: index.php"); exit;
        }
    }
    $error = "Access denied.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Vlyx Hub</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M30 50 L55 5 L55 45 L80 45 L45 95 L45 55 Z' fill='%2300ddff'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --bg: #1e1e26; --card: #2b2a33; --accent: #00ddff; --text: #fbfbfe; }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .logo-container { display: flex; align-items: center; margin-bottom: 40px; }
        .brand-logo { font-size: 4rem; color: var(--accent); margin-right: 20px; }
        .brand-text { font-size: 3.5rem; font-weight: bold; }
        .login-box { width: 100%; max-width: 420px; text-align: center; }
        .input-group { position: relative; margin-bottom: 15px; }
        input { width: 100%; padding: 15px 45px 15px 20px; border-radius: 12px; border: 1px solid #333; background: #23222b; color: white; box-sizing: border-box; font-size: 1rem; outline: none; transition: 0.3s; }
        input:focus { border-color: var(--accent); }
        .input-icon { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--accent); opacity: 0.7; }
        .btn-unlock { background: var(--accent); color: #1e1e26; font-weight: 800; border: none; padding: 16px; border-radius: 12px; cursor: pointer; width: 100%; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; box-shadow: 0 4px 15px rgba(0, 221, 255, 0.3); margin-top: 10px; }
        .btn-unlock:hover { background: #00b8d4; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0, 221, 255, 0.5); }
        .footer { position: fixed; bottom: 30px; color: #444; font-size: 0.8rem; font-weight: bold; }
    </style>
</head>
<body>
    <div class="logo-container">
        <div class="brand-logo"><i class="fa-solid fa-bolt-lightning"></i></div>
        <div class="brand-text">Vlyx</div>
    </div>
    <div class="login-box">
        <form method="POST">
            <div class="input-group">
                <input type="text" name="user" placeholder="Username or Email" required autofocus>
                <i class="fa-solid fa-at input-icon"></i>
            </div>
            <div class="input-group">
                <input type="password" name="pass" placeholder="Password" required>
                <i class="fa-solid fa-lock input-icon"></i>
            </div>
            <button type="submit" class="btn-unlock">Unlock Hub</button>
        </form>
        <?php if(isset($error)) echo "<p style='color:#ff4444; margin-top:20px; font-weight:bold;'>$error</p>"; ?>
        <a href="forgot.php" style="color:#666; display:block; margin-top:25px; text-decoration:none; font-size:0.9rem;">Forgot Password?</a>
    </div>
    <div class="footer">Vlyx built by Aldon &bull; 2026 &bull; <a href="https://github.com/ByAldon/vlyx" target="_blank" style="color:#555; text-decoration:none;">GitHub</a></div>
</body>
</html>