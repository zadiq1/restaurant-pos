
<?php
declare(strict_types=1);
session_start();

function get_db(): PDO {
    static $db = null;
    if ($db) return $db;
    $dbPath = __DIR__ . '/pos.sqlite';
    $dsn = 'sqlite:' . $dbPath;
    $db = new PDO($dsn);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function json_response($data, int $status=200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function require_login($roles = []) {
    if (!isset($_SESSION['user'])) {
        header('Location: /index.php'); exit;
    }
    if ($roles && !in_array($_SESSION['user']['role'], $roles)) {
        http_response_code(403); echo 'Forbidden'; exit;
    }
}

function cents(int|float $amount): int {
    return (int) round($amount);
}

function price_format(int $cents): string {
    return number_format($cents / 100, 2);
}

function get_setting(string $key, $default=null) {
    $db = get_db();
    $stmt = $db->prepare('SELECT value FROM settings WHERE key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) return $default;
    $val = $row['value'];
    $json = json_decode($val, true);
    return (json_last_error() === JSON_ERROR_NONE) ? $json : $val;
}

function set_setting(string $key, $value): void {
    $db = get_db();
    $val = is_string($value) ? $value : json_encode($value);
    $stmt = $db->prepare('INSERT INTO settings(key,value) VALUES(?,?) ON CONFLICT(key) DO UPDATE SET value=excluded.value');
    $stmt->execute([$key, $val]);
}

function get_settings(): array {
    $db = get_db();
    $rows = $db->query('SELECT key, value FROM settings')->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $r) {
        $json = json_decode($r['value'], true);
        $out[$r['key']] = (json_last_error() === JSON_ERROR_NONE) ? $json : $r['value'];
    }
    return $out;
}
