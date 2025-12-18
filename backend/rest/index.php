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

Flight::set('auth_middleware', new AuthMiddleware());

Flight::before('start', function (&$params, &$output) { // JWT Authentication Middleware. parameters passed by reference so we can modify them if needed

  $path = Flight::request()->url;

if (
  $path === '/' ||
  $path === '' ||
  strpos($path, '/favicon.ico') === 0 ||
  strpos($path, '/auth/login') === 0 ||
  strpos($path, '/auth/register') === 0 ||
  strpos($path, '/docs') === 0 ||
  strpos($path, '/openapi') === 0
) {
  return;
}

// Get Authorization header and validate JWT 
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
//
    if (!$tokenHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
      $tokenHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (!$tokenHeader) {
      Flight::halt(401, 'Missing Authorization header');
    }

    if (strpos($tokenHeader, 'Bearer ') === 0) { // Extract token from "Bearer <token
      $token = substr($tokenHeader, 7);
    } else {
      $token = $tokenHeader;
    }

    $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));// Decode JWT token

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