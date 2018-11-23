<?php

use \Toolkit\{
    DataAccess\Database,
    SessionHandler\DatabaseSession
};

require __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../../config/config.php';


new DatabaseSession(new Database($config['database']));

session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Session Test</title>
</head>
<body>

<h1>Database Session Test Page 2</h1>

<p>
    <?= "Session set to {$_SESSION['module']}"; ?>
</p>

<?php
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', 1,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();
?>
<p>
    Session has been destroyed
</p>
<p>
    <?= $_SESSION['module'] ?? 'No session available'; ?>
</p>

<a href="Page1.php">First Page</a>

</body>
</html>
