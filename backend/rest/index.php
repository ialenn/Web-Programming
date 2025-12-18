<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit();
}

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/config.php';
require __DIR__ . '/dao/BaseDao.php';
require __DIR__ . '/middleware/AuthMiddleware.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::route('GET /', function () {
  Flight::json([
    "status" => "OK",
    "message" => "API is running",
    "routes" => [
      "POST /auth/login",
      "POST /auth/register",
      "GET /events",
      "GET /v1/docs"
    ]
  ]);
});

Flight::set('auth_middleware', new AuthMiddleware());

Flight::before('start', function (&$params, &$output) {

  $path = Flight::request()->url;

  if (
    $path === '/' ||
    $path === '' ||
    strpos($path, '/favicon.ico') === 0 ||
    strpos($path, '/auth/login') === 0 ||
    strpos($path, '/auth/register') === 0 ||
    strpos($path, '/public/v1/docs') === 0 ||
    strpos($path, '/openapi') === 0 ||
  strpos($path, '/public/openapi') === 0
    
  ) {
    return;
  }

  try {
    $tokenHeader = null;

    if (function_exists('getallheaders')) {
      $headers = getallheaders();
      if (isset($headers['Authorization'])) {
        $tokenHeader = $headers['Authorization'];
      } else if (isset($headers['authorization'])) {
        $tokenHeader = $headers['authorization'];
      }
    }

    if (!$tokenHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
      $tokenHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (!$tokenHeader) {
      Flight::halt(401, 'Missing Authorization header');
    }

    if (strpos($tokenHeader, 'Bearer ') === 0) {
      $token = substr($tokenHeader, 7);
    } else {
      $token = $tokenHeader;
    }

    $decoded = JWT::decode(
      $token,
      new Key(Config::JWT_SECRET(), 'HS256')
    );

    Flight::set('user', $decoded->user);
    Flight::set('jwt_token', $token);

  } catch (Exception $e) {
    Flight::halt(401, $e->getMessage());
  }
});


require __DIR__ . '/routes/auth.php';
require __DIR__ . '/routes/events.php';
require __DIR__ . '/routes/users.php';
require __DIR__ . '/routes/venues.php';
require __DIR__ . '/routes/tickets.php';
require __DIR__ . '/routes/roles.php';

Flight::start();
?>