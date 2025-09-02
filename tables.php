
<?php
require __DIR__ . '/../config.php';
require_login(['admin','cashier','waiter']);
$db = get_db();
if ($_SERVER['REQUEST_METHOD']==='GET') {
    $rows = $db->query('SELECT * FROM tables WHERE active=1 ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    json_response(['items'=>$rows]);
} else if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_login(['admin']);
    $data = json_decode(file_get_contents('php://input'), true);
    $label = trim($data['label'] ?? '');
    $cap = (int)($data['capacity'] ?? 4);
    $stmt = $db->prepare('INSERT INTO tables (label,capacity,active) VALUES (?,?,1)');
    $stmt->execute([$label,$cap]);
    json_response(['ok'=>true]);
}
