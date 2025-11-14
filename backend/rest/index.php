<?php
require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/config.php';
require __DIR__ . '/dao/BaseDao.php';

require __DIR__ . '/routes/events.php';
require __DIR__ . '/routes/users.php';
require __DIR__ . '/routes/venues.php';
require __DIR__ . '/routes/tickets.php';
require __DIR__ . '/routes/roles.php';

Flight::start();
?>