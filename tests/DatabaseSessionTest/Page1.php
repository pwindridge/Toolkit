<?php

use \Toolkit\{
    DataAccess\Database,
    SessionHandler\DatabaseSession
};

require __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../../config/config.php';


new DatabaseSession(
        new Database($config['database'])
);

session_start();

$_SESSION['module'] = 'Server Side Scripting';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Test Database Session</title>
</head>

<body>

<h1>Database Session Test Page 1</h1>

<p>$_SESSION['module'] set to <?= $_SESSION['module']; ?></p>

<a href="Page2.php">Second Page</a>
</body>
</html>
