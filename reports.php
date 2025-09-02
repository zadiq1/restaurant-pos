
<?php
require __DIR__ . '/../config.php';
require_login(['admin','cashier']);
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Reports</title><link rel="stylesheet" href="/assets/styles.css"><script defer src="/js/app.js"></script></head>
<body>
<header class="topbar"><div>Reports</div><nav><a href="/dashboard.php">Dashboard</a><a href="/logout.php">Logout</a></nav></header>
<main class="container"><h3>Daily Sales</h3><div><label>Date</label><input type="date" id="rep-date"><button onclick="loadDaily()">Load</button></div><div id="rep-output"></div></main>
</body></html>
