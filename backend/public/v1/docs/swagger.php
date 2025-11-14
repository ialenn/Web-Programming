<?php

require __DIR__ . '/../../../rest/vendor/autoload.php';

define('LOCALSERVER', 'http://localhost/AlenIkanovic/Web-Programming/backend/rest');
define('PRODSERVER', 'https://add-production-server-after-deployment/backend/rest');

// Determine the base URL
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    define('BASE_URL', LOCALSERVER);
} else {
    define('BASE_URL', PRODSERVER);
}

// this will include doc_setup.php and all route files in the routes directory
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',
    __DIR__ . '/../../../rest/routes'
]);

header('Content-Type: application/json');
echo $openapi->toJson();
?>