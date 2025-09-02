
<?php
require __DIR__ . '/../config.php';
require_login(['admin','cashier','waiter','kitchen']);
$user = $_SESSION['user'];
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Dashboard</title><link rel="stylesheet" href="/assets/styles.css"><script defer src="/js/app.js"></script></head>
<body>
<header class="topbar">
  <div>POS Dashboard</div>
  <nav>
    <a href="/pos.php">POS</a>
    <a href="/kitchen.php">Kitchen</a>
    <?php if (in_array($user['role'], ['admin','cashier'])): ?>
      <a href="/reports.php">Reports</a>
    <?php endif; ?>
    <?php if ($user['role']==='admin'): ?>
      <a href="#" data-open="menu">Menu</a>
      <a href="#" data-open="tables">Tables</a>
      <a href="#" data-open="users">Users</a>
      <a href="/settings.php">Settings</a>
    <?php endif; ?>
    <a href="/logout.php">Logout</a>
  </nav>
</header>
<main class="container">
  <h2>Welcome, <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)</h2>
  <?php if ($user['role']==='admin'): ?>
  <section id="admin-panels" class="grid">
    <div class="panel"><h3>Menu</h3><div id="menu-list"></div><button onclick="openMenuModal()">Add Item</button></div>
    <div class="panel"><h3>Tables</h3><div id="table-list"></div><button onclick="addTable()">Add Table</button></div>
    <div class="panel"><h3>Users</h3><div><em>Seed users created. Manage via DB in MVP.</em></div></div>
  </section>
  <?php else: ?>
  <p>Use the navigation above to access your screens.</p>
  <?php endif; ?>
</main>
<div id="menu-modal" class="modal hidden"><div class="modal-content"><h3>Menu Item</h3><form id="menu-form" onsubmit="return saveMenuItem(event)"><input type="hidden" name="id" id="menu-id"><label>Name</label><input name="name" id="menu-name" required><label>Price (e.g. 8.50)</label><input name="price" id="menu-price" required><label>Tax Rate (e.g. 0.05)</label><input name="tax_rate" id="menu-tax" value="0.05"><button type="submit">Save</button><button type="button" onclick="closeMenuModal()">Cancel</button></form></div></div>
</body></html>
