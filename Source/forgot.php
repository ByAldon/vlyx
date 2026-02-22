<?php
session_start();
$usersFile = 'users.json';
$tokensFile = 'tokens.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $userData = json_decode(file_get_contents($usersFile), true);
    $foundUser = null;

    foreach ($userData as $username => $info) {
        if (isset($info['email']) && strtolower($info['email']) === strtolower($email)) {
            $foundUser = $username;
            break;
        }
    }

    if ($foundUser) {
        $token = bin2hex(random_bytes(20));
        $expiry = time() + 3600;

        $tokens = file_exists($tokensFile) ? json_decode(file_get_contents($tokensFile), true) : [];
        $tokens[$token] = ['user' => $foundUser, 'expires' => $expiry];
        file_put_contents($tokensFile, json_encode($tokens, JSON_PRETTY_PRINT));

        // AFZENDER GEFIXEERD OP life42.nl
        $resetLink = "https://life42.nl/bookmarks/reset.php?token=" . $token;
        $subject = "Password Reset - My Space";
        $message = "Hello " . htmlspecialchars($foundUser) . ",\r\n\r\nClick here to reset your password:\r\n" . $resetLink;
        
        // Header handmatig ingesteld
        $headers = "From: no-reply@life42.nl" . "\r\n" .
                   "Reply-To: no-reply@life42.nl" . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        if (mail($email, $subject, $message, $headers)) {
            $status = "Reset link sent! Please check your inbox.";
        } else {
            $error = "Email failed to send. Check server mail settings.";
        }
    } else {
        $error = "Email not found in our system.";
    }
}
?>