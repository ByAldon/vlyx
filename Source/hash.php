<?php
// type in your password below and open this file in your browser (be sure to upload it online first). It then will show a hash
$wachtwoord = 'your-password-here'; 

echo "Copy the hash below and paste this in users.json:<br><br>";
echo "<b>" . password_hash($wachtwoord, PASSWORD_DEFAULT) . "</b>";
?>