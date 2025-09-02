
<?php
require __DIR__ . '/../config.php';
require_login(['admin','cashier','waiter','kitchen']);
$db = get_db();
if ($_SERVER['REQUEST_METHOD']==='GET') {
    if (($_GET['scope'] ?? '')==='kds') {
        $rows = $db->query('SELECT oi.*, mi.name AS item_name FROM order_items oi JOIN menu_items mi ON mi.id=oi.item_id WHERE oi.kds_status IN ("queued","in_progress") ORDER BY oi.id DESC')->fetchAll(PDO::FETCH_ASSOC);
        json_response(['items'=>$rows]);
    }
    json_response(['items'=>[]]);
}
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
if ($action==='send') {
    require_login(['admin','cashier','waiter']);
    $db->beginTransaction();
    try {
        $stmt = $db->prepare('INSERT INTO orders (table_id,user_id,status) VALUES (?,?, "sent")');
        $stmt->execute([(int)$data['table_id'], (int)$_SESSION['user']['id']]);
        $order_id = (int)$db->lastInsertId();
        $items = $data['items'] ?? [];
        $ins = $db->prepare('INSERT INTO order_items (order_id,item_id,qty,price_cents,tax_rate) VALUES (?,?,?,?,?)');
        foreach ($items as $it) {
            $ins->execute([$order_id,(int)$it['item_id'],(int)$it['qty'],(int)$it['price_cents'],(float)$it['tax_rate']]);
        }
        $db->commit();
        json_response(['ok'=>true,'order_id'=>$order_id]);
    } catch (Throwable $e) {
        $db->rollBack();
        json_response(['error'=>$e->getMessage()], 500);
    }
}
if ($action==='kds') {
    require_login(['admin','kitchen']);
    $stmt = $db->prepare('UPDATE order_items SET kds_status=? WHERE id=?');
    $stmt->execute([$data['status'] ?? 'queued', (int)$data['item_id']]);
    json_response(['ok'=>true]);
}
if ($action==='pay') {
    require_login(['admin','cashier']);
    $order_id = (int)$data['order_id'];
    $amount_cents = (int) round(($data['amount'] ?? 0) * 100);
    $method = $data['method'] ?? 'cash';
    $reference = $data['reference'] ?? null;
    $settings = get_settings();
    $allowed = $settings['payment_methods'] ?? ['cash','card','zaad','edahab'];
    if (!in_array($method, $allowed)) { json_response(['error'=>'Payment method not allowed'], 400); }
    $items = $db->prepare('SELECT qty, price_cents, tax_rate FROM order_items WHERE order_id=?');
    $items->execute([$order_id]);
    $subtotal = 0; $tax = 0;
    foreach ($items->fetchAll(PDO::FETCH_ASSOC) as $it) {
        $line = $it['qty'] * $it['price_cents'];
        $subtotal += $line;
        $tax += (int) round($line * $it['tax_rate']);
    }
    if (($settings['tax_mode'] ?? 'per_item') === 'global') {
        $tax = (int) round($subtotal * (float)($settings['vat_rate'] ?? 0));
    }
    $total = $subtotal + $tax;
    $db->beginTransaction();
    try {
        $ins = $db->prepare('INSERT INTO payments (order_id,method,amount_cents) VALUES (?,?,?)');
        $ins->execute([$order_id,$method,$amount_cents]);
        $payment_id = (int)$db->lastInsertId();
        if ($reference) {
            $m = $db->prepare('INSERT INTO payment_meta (payment_id, meta_key, meta_value) VALUES (?,?,?)');
            $m->execute([$payment_id, 'reference', $reference]);
        }
        $upd = $db->prepare('UPDATE orders SET status="paid", closed_at=CURRENT_TIMESTAMP WHERE id=?');
        $upd->execute([$order_id]);
        $db->commit();
        json_response(['ok'=>true,'total_cents'=>$total]);
    } catch (Throwable $e) {
        $db->rollBack();
        json_response(['error'=>$e->getMessage()], 500);
    }
}
json_response(['error'=>'Unknown action'], 400);
