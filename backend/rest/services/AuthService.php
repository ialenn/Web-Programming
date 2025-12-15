<?php
// this filehandles user registration and login including JWT token generation
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key; //

class AuthService extends BaseService {//

  private $auth_dao;

  public function __construct() {
    $this->auth_dao = new AuthDao();
    parent::__construct($this->auth_dao);
  }

  public function register($data) { // user registration method
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) { 
      return array(
        'success' => false,
        'error'   => 'Name, email and password are required.'
      );
    }

    $existing = $this->auth_dao->get_user_by_email($data['email']); 
    if ($existing) {
      return array(
        'success' => false,
        'error'   => 'Email already registered.'
      );
    }

    if (empty($data['role_id'])) {
      $data['role_id'] = 2;
    }

    $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT); // hash the password 
    unset($data['password']);

    $user_id = $this->auth_dao->insert($data);
    $user = $this->auth_dao->get_by_id($user_id);
    unset($user['password_hash']);

    return array(
      'success' => true,
      'user'    => $user
    );
  }

  public function login($data) { // user login method
    if (empty($data['email']) || empty($data['password'])) {
      return array(
        'success' => false,
        'error'   => 'Email and password are required.'
      );
    }

    $user = $this->auth_dao->get_user_by_email($data['email']);

    if (!$user) {
      return array(
        'success' => false,
        'error'   => 'Invalid email or password.'
      );
    }

    if (!password_verify($data['password'], $user['password_hash'])) {
      return array(
        'success' => false,
        'error'   => 'Invalid email or password.'
      );
    }

    unset($user['password_hash']);

    $payload = array(
      'user' => $user,
      'iat'  => time(),
      'exp'  => time() + 60 * 60 * 24 // valid 1 day
    );

    $token = JWT::encode($payload, Config::JWT_SECRET(), 'HS256');

    return array(
      'success' => true,
      'token'   => $token,
      'user'    => $user
    );
  }
}
