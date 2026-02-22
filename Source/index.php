<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$user = $_SESSION['user'];
$role = $_SESSION['role'] ?? 'user';
$file = "users/" . $user . ".json";

if (!is_dir('users')) { mkdir('users', 0777, true); }
if (!file_exists($file)) { file_put_contents($file, json_encode([])); }
$links = json_decode(file_get_contents($file), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) { 
        $links[] = ['name' => $_POST['name'], 'url' => $_POST['url']]; 
    } elseif (isset($_POST['delete_link'])) { 
        array_splice($links, (int)$_POST['index'], 1); 
    }
    file_put_contents($file, json_encode($links));
    header("Location: index.php"); exit;
}
if (isset($_GET['logout'])) { session_destroy(); header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($user) ?>'s Vlyx</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>?</text></svg>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg: #2b2a33; --card: #42414d; --accent: #00ddff; --text: #fbfbfe; }
        body { background: var(--bg); color: var(--text); font-family: sans-serif; display: flex; flex-direction: column; align-items: center; padding: 50px 20px; margin: 0; min-height: 100vh; }
        .logo { font-size: 2.5rem; margin-bottom: 50px; font-weight: bold; display: flex; align-items: center; }
        .logo i { color: var(--accent); margin-right: 15px; }
        .logo span { color: #aaa; font-weight: normal; margin-right: 10px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 25px; width: 100%; max-width: 900px; }
        .bookmark-wrapper { position: relative; text-align: center; background: var(--card); padding: 20px; border-radius: 18px; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.4); }
        .bookmark-wrapper:hover { transform: translateY(-5px); background: #4e4d5a; }
        .icon-box { width: 55px; height: 55px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 12px; }
        .icon-box img { width: 32px; height: 32px; object-fit: contain; }
        .del-btn { position: absolute; top: 10px; right: 10px; background: none; border: none; color: #ff4444; cursor: pointer; opacity: 0.4; transition: 0.2s; }
        .del-btn:hover { opacity: 1; }
        .admin-bar { position: fixed; bottom: 0; width: 100%; background: #1a1a20; padding: 15px; text-align: center; border-top: 2px solid var(--accent); box-sizing: border-box; }
        input { padding: 12px; border-radius: 8px; border: none; background: #2b2a33; color: white; margin-right: 10px; width: 200px; }
        button.add { background: var(--accent); border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; color: #2b2a33; }
        .nav-link { color: var(--accent); text-decoration: none; font-weight: bold; margin-left: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="logo"><i class="fas fa-bolt-lightning"></i><span><?= htmlspecialchars($user) ?>'s</span> Vlyx</div>
    <div class="grid">
        <?php foreach ($links as $index => $link): ?>
            <div class="bookmark-wrapper">
                <form method="POST">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" name="delete_link" class="del-btn" onclick="return confirm('Delete link?')"><i class="fas fa-trash-can"></i></button>
                </form>
                <a href="<?= htmlspecialchars($link['url']) ?>" style="text-decoration:none; color:inherit;" target="_blank">
                    <div class="icon-box"><img src="https://www.google.com/s2/favicons?sz=64&domain=<?= parse_url($link['url'], PHP_URL_HOST) ?>" alt=""></div>
                    <div style="font-weight:bold;"><?= htmlspecialchars($link['name']) ?></div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="admin-bar">
        <form method="POST">
            <input type="text" name="name" placeholder="Site Name" required>
            <input type="url" name="url" placeholder="https://..." required>
            <button type="submit" name="add" class="add">Add to Vlyx</button>
            <a href="profile.php" class="nav-link"><i class="fas fa-user-gear"></i> Settings</a>
            <?php if ($role === 'admin'): ?><a href="admin.php" class="nav-link"><i class="fas fa-shield-halved"></i> Admin</a><?php endif; ?>
            <a href="?logout" class="nav-link" style="color:#888;"><i class="fas fa-power-off"></i></a>
        </form>
    </div>
</body>
</html>