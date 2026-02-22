<?php
$token = $_GET['token'] ?? '';
$tokens = json_decode(file_get_contents('tokens.json'), true) ?: [];
if (!isset($tokens[$token]) || $tokens[$token]['expires'] < time()) { die("Invalid or expired token."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = json_decode(file_get_contents('users.json'), true);
    $user = $tokens[$token]['user'];
    $users[$user]['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    unset($tokens[$token]);
    file_put_contents('tokens.json', json_encode($tokens));
    header("Location: login.php?success=1"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Update Password - Vlyx</title>
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
        <h2>New Password</h2>
        <form method="POST">
            <input type="password" name="password" placeholder="Enter new password" required>
            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>