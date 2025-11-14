<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService {

  public function __construct() {
    parent::__construct(new UserDao());
  }

  // READ helpers
  public function get_all() {
    return $this->dao->get_all();
  }

  public function get_by_id($id) {
    return $this->dao->get_by_id($id);
  }

  // CREATE
  public function create($data) {
    if (empty($data['name']))  throw new Exception("name is required");
    if (empty($data['email'])) throw new Exception("email is required");

    // either raw password or already hashed value
    if (!empty($data['password'])) {
      $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
      unset($data['password']);
    }
    if (empty($data['password_hash'])) {
      throw new Exception("password or password_hash is required");
    }

    if (empty($data['role_id'])) {
      $data['role_id'] = 1;
    }

    $id = parent::create($data);
    return $this->dao->get_by_id($id);
  }

  // UPDATE
  public function update($id, $data) {
    if (!$this->dao->get_by_id($id)) {
      throw new Exception("user not found");
    }

    if (!empty($data['password'])) {
      $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
      unset($data['password']);
    }

    parent::update($id, $data);
    return $this->dao->get_by_id($id);
  }

  // DELETE
  public function delete($id) {
    if (!$this->dao->get_by_id($id)) {
      throw new Exception("user not found");
    }

    return parent::delete($id);
  }
}
?>