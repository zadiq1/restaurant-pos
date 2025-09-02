
<?php
require __DIR__ . '/../config.php';
require_login(['admin','cashier','waiter','kitchen']);
$db = get_db();
if ($_SERVER['REQUEST_METHOD']==='GET') {
    $rows = $db->query('SELECT id, name, price_cents, tax_rate FROM menu_items ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    json_response(['items'=>$rows]);
} else if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_login(['admin']);
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $price = (int) round(($data['price'] ?? 0)*100);
    $tax = (float) ($data['tax_rate'] ?? 0);
    if (!empty($data['id'])) {
        $stmt = $db->prepare('UPDATE menu_items SET name=?, price_cents=?, tax_rate=? WHERE id=?');
        $stmt->execute([$name,$price,$tax,(int)$data['id']]);
    } else {
        $stmt = $db->prepare('INSERT INTO menu_items (name, price_cents, tax_rate) VALUES (?,?,?)');
        $stmt->execute([$name,$price,$tax]);
    }
    json_response(['ok'=>true]);
}
