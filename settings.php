
<?php
require __DIR__ . '/../config.php';
require_login(['admin']);
$settings = get_settings();
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Settings</title><link rel="stylesheet" href="/assets/styles.css"><script defer src="/js/app.js"></script></head>
<body>
<header class="topbar"><div>Settings</div><nav><a href="/dashboard.php">Dashboard</a><a href="/logout.php">Logout</a></nav></header>
<main class="container">
  <form id="settings-form" onsubmit="return saveSettings(event)">
    <div class="grid">
      <div class="panel"><h3>Restaurant Info</h3><label>Name</label><input name="restaurant_name" value="<?= htmlspecialchars($settings['restaurant_name'] ?? '') ?>"><label>Address</label><input name="restaurant_address" value="<?= htmlspecialchars($settings['restaurant_address'] ?? '') ?>"><label>Phone</label><input name="restaurant_phone" value="<?= htmlspecialchars($settings['restaurant_phone'] ?? '') ?>"><label>Receipt Footer</label><input name="receipt_footer" value="<?= htmlspecialchars($settings['receipt_footer'] ?? '') ?>"></div>
      <div class="panel"><h3>Tax & Currency</h3><label>Currency Symbol</label><input name="currency_symbol" value="<?= htmlspecialchars($settings['currency_symbol'] ?? '$') ?>"><label>VAT Rate (e.g. 0.05 for 5%)</label><input name="vat_rate" value="<?= htmlspecialchars($settings['vat_rate'] ?? '0.05') ?>"><label>Tax Mode</label><select name="tax_mode"><?php $mode = $settings['tax_mode'] ?? 'per_item'; ?><option value="per_item" <?= $mode==='per_item'?'selected':'' ?>>Per Item</option><option value="global" <?= $mode==='global'?'selected':'' ?>>Global VAT (subtotal)</option></select></div>
      <div class="panel"><h3>Payment Methods</h3><?php $methods = $settings['payment_methods'] ?? ['cash','card','zaad','edahab']; $all = ['cash'=>'Cash','card'=>'Card','zaad'=>'Zaad','edahab'=>'eDahab']; ?><?php foreach ($all as $k=>$label): ?><div><label><input type="checkbox" name="payment_methods[]" value="<?= $k ?>" <?= in_array($k,$methods)?'checked':'' ?>> <?= $label ?></label></div><?php endforeach; ?></div>
    </div>
    <button type="submit">Save Settings</button>
  </form>
</main>
</body></html>
