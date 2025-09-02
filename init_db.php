
<?php
require __DIR__.'/config.php';
$db = get_db();
$sql = file_get_contents(__DIR__.'/db.sql');
$db->exec($sql);
echo "<h2>Database initialized.</h2>";
echo "<p>Logins:</p>";
echo "<ul>
<li>admin@example.com / admin123</li>
<li>cashier@example.com / cashier123</li>
<li>waiter@example.com / waiter123</li>
<li>kitchen@example.com / kitchen123</li>
</ul>";
echo '<p><a href="/index.php">Go to Login</a></p>';
