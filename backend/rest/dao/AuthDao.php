<?php
// Data Access Object for authentication related database operations
require_once __DIR__ . '/BaseDao.php';

class AuthDao extends BaseDao {

  public function __construct() {
    parent::__construct('users');
  }

  public function get_user_by_email($email) {
    $sql = "SELECT u.*, r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute(array('email' => $email));
    return $stmt->fetch();
  }
}
?>