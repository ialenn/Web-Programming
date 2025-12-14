<?php
//define the authentication middleware functions
class AuthMiddleware {

  public static function require_login() {
    $user = Flight::get('user');
    if (!$user) {
      Flight::halt(401, 'Login required.');
    }
  }

  public static function require_admin() {
    self::require_login();

    $user = Flight::get('user');

    if (!isset($user->role_id) || $user->role_id != 1) {
      Flight::halt(403, 'Admin access only.');
    }
  }

  public static function validate_required($fields) {
    $data = Flight::request()->data->getData();

    foreach ($fields as $field) {
      if (!isset($data[$field]) || $data[$field] === '') {
        Flight::halt(400, "Field '$field' is required.");
      }
    }
  }
}
?>