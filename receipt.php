
<?php
require __DIR__ . '/../config.php';
require_login(['admin','cashier','waiter']);
$id = (int)($_GET['order_id'] ?? 0);
$db = get_db();
$o = $db->prepare('SELECT * FROM orders WHERE id=?');
$o->execute([$id]);
$order = $o->fetch(PDO::FETCH_ASSOC);
if (!$order) { echo "Order not found"; exit; }
$items = $db->prepare('SELECT oi.*, mi.name FROM order_items oi JOIN menu_items mi ON mi.id = oi.item_id WHERE order_id=?');
$items->execute([$id]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);
$payments = $db->prepare('SELECT * FROM payments WHERE order_id=?');
$payments->execute([$id]);
$payments = $payments->fetchAll(PDO::FETCH_ASSOC);
$name=get_setting('restaurant_name','RESTAURANT');
$addr=get_setting('restaurant_address','');
$phone=get_setting('restaurant_phone','');
$cur=get_setting('currency_symbol','$');
$vat_rate=get_setting('vat_rate',0);
$tax_mode=get_setting('tax_mode','per_item');
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Receipt #<?= $id ?></title><link rel="stylesheet" href="/assets/styles.css"><style>@media print { .no-print { display: none; } .receipt { width: 80mm; } }</style></head>
<body>
<div class="receipt">
  <h2><?= htmlspecialchars($name) ?></h2>
  <p><?= htmlspecialchars($addr) ?><br><?= htmlspecialchars($phone) ?></p>
  <p>Order #<?= $id ?> • <?= htmlspecialchars($order['created_at']) ?></p>
  <hr>
  <table style="width:100%">
    <?php
    $subtotal=0; $tax=0;
    foreach ($items as $it):
      $line = $it['price_cents']*$it['qty'];
      $subtotal += $line;
      $tax += (int) round($line * $it['tax_rate']);
    ?>
    <tr><td><?= htmlspecialchars($it['name']) ?> x <?= (int)$it['qty'] ?></td><td style="text-align:right"><?= $cur ?><?= number_format($line/100,2) ?></td></tr>
    <?php endforeach; ?>
  </table>
  <hr>
  <?php if ($tax_mode==='global') { $tax = (int) round($subtotal * (float)$vat_rate); } ?>
  <p>Subtotal: <?= $cur ?><?= number_format($subtotal/100,2) ?></p>
  <p>VAT/Tax: <?= $cur ?><?= number_format($tax/100,2) ?></p>
  <p><strong>Total: <?= $cur ?><?= number_format(($subtotal+$tax)/100,2) ?></strong></p>
  <hr>
  <p>Payments:</p>
  <?php foreach ($payments as $p): ?>
    <?php $pm = $db->prepare('SELECT meta_value FROM payment_meta WHERE payment_id=? AND meta_key="reference"'); $pm->execute([$p['id']]); $ref=$pm->fetchColumn(); ?>
    <div><?= htmlspecialchars(strtoupper($p['method'])) ?> <?= $ref? '(' . htmlspecialchars($ref) . ')' : '' ?> <?= $cur ?><?= number_format($p['amount_cents']/100,2) ?></div>
  <?php endforeach; ?>
  <hr>
  <?php $footer=get_setting('receipt_footer',''); if ($footer): ?><p><?= htmlspecialchars($footer) ?></p><?php endif; ?>
  <p>Thank you!</p>
</div>
<div class="no-print" style="margin-top:1rem"><button onclick="window.print()">Print</button></div>
</body></html>
