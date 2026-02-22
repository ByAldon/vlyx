<?php
session_start();
$usersFile = 'users.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUser = trim($_POST['user'] ?? '');
    $inputPass = $_POST['pass'] ?? '';
    $userData = json_decode(file_get_contents($usersFile), true);
    $foundUsername = null;

    if (is_array($userData)) {
        foreach ($userData as $username => $info) {
            $emailMatch = (isset($info['email']) && strtolower($inputUser) === strtolower($info['email']));
            $userMatch = (strtolower($inputUser) === strtolower($username));
            if (($userMatch || $emailMatch) && password_verify($inputPass, $info['password'])) {
                $foundUsername = $username;
                $userRole = $info['role'] ?? 'user';
                break;
            }
        }
    }
    if ($foundUsername) {
        $_SESSION['user'] = $foundUsername;
        $_SESSION['role'] = $userRole;
        header("Location: index.php"); exit;
    } else { $error = "Invalid login credentials."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Vlyx</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #2b2a33; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #42414d; padding: 40px; border-radius: 20px; text-align: center; width: 340px; box-shadow: 0 15px 35px rgba(0,0,0,0.6); }
        .logo-login { font-size: 2.5rem; font-weight: bold; margin-bottom: 30px; }
        .logo-login i { color: #00ddff; }
        input { width: 100%; padding: 14px; margin: 10px 0; border-radius: 8px; border: none; background: #2b2a33; color: white; box-sizing: border-box; }
        button { width: 100%; padding: 14px; background: #00ddff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; color: #2b2a33; margin-top: 15px; }
        .error { color: #ff4444; margin-top: 15px; }
        a { color: #888; text-decoration: none; font-size: 0.85rem; margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-login"><i class="fas fa-bolt-lightning"></i> Vlyx</div>
        <form method="POST">
            <input type="text" name="user" placeholder="Username or Email" required autofocus>
            <input type="password" name="pass" placeholder="Password" required>
            <button type="submit">Unlock Hub</button>
        </form>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <a href="forgot.php">Forgot Password?</a>
    </div>
</body>
</html>