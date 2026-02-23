<?php
session_start();
$version = "1.1.5";
$repo_url = "https://github.com/ByAldon/vlyx";
$version_check_url = "https://raw.githubusercontent.com/ByAldon/vlyx/main/version.txt";

// --- AUTO-INSTALLER LOGIC ---
if (!is_dir('users')) { mkdir('users', 0777, true); }
if (!file_exists('users.json')) { file_put_contents('users.json', json_encode([])); }
if (!file_exists('version.txt')) { file_put_contents('version.txt', $version); }

$usersFile = 'users.json';
$userData = json_decode(file_get_contents($usersFile), true) ?: [];

// Check for updates
if (!isset($_SESSION['latest_version'])) {
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $remote_v = @file_get_contents($version_check_url, false, $ctx);
    $_SESSION['latest_version'] = $remote_v ? trim($remote_v) : $version;
}
$is_outdated = version_compare($_SESSION['latest_version'], $version, '>');

// Redirect logic
if (empty($userData)) { header("Location: admin.php"); exit; }
if (isset($_GET['logout'])) { session_destroy(); header("Location: login.php"); exit; }
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$user = $_SESSION['user'];
$role = $_SESSION['role'] ?? 'user';
$userLinksFile = "users/" . $user . ".json";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_link'])) {
        $links = file_exists($userLinksFile) ? json_decode(file_get_contents($userLinksFile), true) : [];
        $links[] = ['name' => $_POST['name'], 'url' => $_POST['url']];
        file_put_contents($userLinksFile, json_encode($links));
    } elseif (isset($_POST['delete_link'])) {
        $links = json_decode(file_get_contents($userLinksFile), true);
        array_splice($links, (int)$_POST['index'], 1);
        file_put_contents($userLinksFile, json_encode($links));
    } elseif (isset($_POST['update_profile'])) {
        $newName = trim($_POST['new_username']);
        if ($newName !== $user) {
            if (file_exists($userLinksFile)) rename($userLinksFile, "users/$newName.json");
            $userData[$newName] = $userData[$user]; unset($userData[$user]);
            $_SESSION['user'] = $newName;
        }
        $userData[$_SESSION['user']]['email'] = trim($_POST['email']);
        if (!empty($_POST['password'])) {
            $userData[$_SESSION['user']]['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        file_put_contents($usersFile, json_encode($userData, JSON_PRETTY_PRINT));
    }
    header("Location: index.php"); exit;
}
$links = file_exists($userLinksFile) ? json_decode(file_get_contents($userLinksFile), true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Vlyx Hub - <?= htmlspecialchars($user) ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M30 50 L55 5 L55 45 L80 45 L45 95 L45 55 Z' fill='%2300ddff'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --bg: #1e1e26; --card: #2b2a33; --accent: #00ddff; --text: #fbfbfe; --dark-bar: #121217; --update: #ffaa00; }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; align-items: center; margin: 0; min-height: 100vh; }
        .logo-container { display: flex; align-items: center; margin: 60px 0; }
        .brand-logo { font-size: 3.5rem; color: var(--accent); margin-right: 20px; }
        .brand-text { font-size: 2.8rem; font-weight: bold; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 30px; width: 100%; max-width: 1000px; padding: 0 20px 140px; box-sizing: border-box; }
        .bookmark-wrapper { position: relative; text-align: center; background: var(--card); padding: 25px; border-radius: 22px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); transition: 0.3s; }
        .icon-box { width: 60px; height: 60px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.25); border-radius: 15px; }
        .icon-box img { width: 36px; height: 36px; object-fit: contain; }
        .del-btn { position: absolute; top: 12px; right: 12px; background: none; border: none; color: #ff4444; cursor: pointer; opacity: 0.3; transition: 0.2s; }
        .admin-bar { position: fixed; bottom: 0; width: 100%; background: var(--dark-bar); border-top: 1px solid rgba(0, 221, 255, 0.3); z-index: 100; padding: 12px 30px; box-sizing: border-box; display: flex; justify-content: space-between; align-items: center; }
        .admin-form { display: flex; align-items: center; gap: 12px; }
        input { padding: 10px 15px; border-radius: 8px; border: 1px solid #333; background: #1e1e26; color: white; box-sizing: border-box; }
        .nav-link { color: var(--accent); text-decoration: none; font-weight: bold; margin-left: 15px; cursor: pointer; font-size: 0.9rem; }
        .bar-info { color: #444; font-size: 0.75rem; font-weight: bold; text-align: right; line-height: 1.2; }
    </style>
</head>
<body>
    <div class="logo-container">
        <div class="brand-logo"><i class="fa-solid fa-bolt-lightning"></i></div>
        <div class="brand-text"><span><?= htmlspecialchars($user) ?>'s</span> Vlyx</div>
    </div>
    <div class="grid">
        <?php foreach ($links as $index => $link): ?>
            <div class="bookmark-wrapper">
                <form method="POST"><input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" name="delete_link" class="del-btn" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash-can"></i></button>
                </form>
                <a href="<?= htmlspecialchars($link['url']) ?>" style="text-decoration:none; color:inherit;" target="_blank">
                    <div class="icon-box"><img src="https://www.google.com/s2/favicons?sz=64&domain=<?= parse_url($link['url'], PHP_URL_HOST) ?>" alt=""></div>
                    <div style="font-weight:bold;"><?= htmlspecialchars($link['name']) ?></div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="admin-bar">
        <div class="admin-form">
            <form method="POST" style="display:flex; gap:10px;">
                <input type="text" name="name" placeholder="Name" required style="width: 140px;">
                <input type="url" name="url" placeholder="URL" required style="width: 200px;">
                <button type="submit" name="add_link" style="background:var(--accent); border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer; color: #1e1e26;">Add Link</button>
            </form>
            <?php if ($role === 'admin'): ?><a href="admin.php" class="nav-link">Admin</a><?php endif; ?>
            <a href="?logout=1" class="nav-link" style="color:#666;"><i class="fa-solid fa-power-off"></i></a>
        </div>
        <div class="bar-info">
            Vlyx Hub v<?= $version ?> &bull; GitHub<br>
            <?php if ($is_outdated): ?>
                <a href="<?= $repo_url ?>" target="_blank" style="color: var(--update); text-decoration: none;"><i class="fa-solid fa-circle-arrow-up"></i> Update: v<?= htmlspecialchars($_SESSION['latest_version']) ?></a>
            <?php else: ?>
                <span style="color: #333;">System up to date</span>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>