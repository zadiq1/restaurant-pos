
<?php
require __DIR__ . '/../config.php';
require_login(['admin','cashier','waiter']);
$user = $_SESSION['user'];
$db = get_db();
$tables = $db->query('SELECT * FROM tables WHERE active=1 ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$menu = $db->query('SELECT * FROM menu_items ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>POS</title><link rel="stylesheet" href="/assets/styles.css"><script defer src="/js/app.js"></script></head>
<body>
<header class="topbar"><div>POS</div><nav><a href="/dashboard.php">Dashboard</a><a href="/logout.php">Logout</a></nav></header>
<main class="container pos">
  <section class="tables"><h3>Tables</h3><div class="list"><?php foreach($tables as $t): ?><button onclick="openOrder(<?= (int)$t['id'] ?>,'<?= htmlspecialchars($t['label']) ?>')"><?= htmlspecialchars($t['label']) ?></button><?php endforeach; ?></div></section>
  <section class="menu"><h3>Menu</h3><div class="list"><?php foreach($menu as $m): ?><button onclick="addItemToCurrent(<?= (int)$m['id'] ?>,'<?= htmlspecialchars($m['name']) ?>',<?= (int)$m['price_cents'] ?>,<?= (float)$m['tax_rate'] ?>)"><?= htmlspecialchars($m['name']) ?><br><small><?= get_setting('currency_symbol','$') ?><?= number_format($m['price_cents']/100,2) ?></small></button><?php endforeach; ?></div></section>
  <section class="ticket"><h3 id="ticket-title">Ticket</h3><div id="ticket-lines"></div><div class="totals"><div>Subtotal: <span id="subtotal">0.00</span></div><div>VAT/Tax: <span id="tax">0.00</span></div><div>Total: <strong><span id="total">0.00</span></strong></div></div><div class="checkout"><label>Payment Method</label><select id="pay-method"></select><label>Reference (Zaad/eDahab)</label><input id="pay-ref" placeholder="Txn ID (optional)"></div><div class="actions"><button onclick="sendToKitchen()">Send to Kitchen</button><button onclick="payOrder()">Pay</button><button onclick="clearTicket()">Clear</button></div></section>
</main>
</body></html>
