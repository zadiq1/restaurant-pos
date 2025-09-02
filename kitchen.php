
<?php
require __DIR__ . '/../config.php';
require_login(['admin','kitchen']);
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Kitchen Display</title><link rel="stylesheet" href="/assets/styles.css"><script defer src="/js/app.js"></script></head>
<body>
<header class="topbar"><div>Kitchen Display</div><nav><a href="/dashboard.php">Dashboard</a><a href="/logout.php">Logout</a></nav></header>
<main class="container"><div id="kds-board" class="grid"></div></main>
</body></html>
