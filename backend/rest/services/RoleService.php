<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/RoleDao.php';

class RoleService extends BaseService {

  public function __construct() {
    parent::__construct(new RoleDao());
  }

  public function get_all() {
    return $this->dao->get_all();
  }

  public function get_by_id($id) {
    return $this->dao->get_by_id($id);
  }

  // CREATE
  public function create($data) {
    if (empty($data['name'])) {
      throw new Exception("name is required");
    }

    $id = parent::create($data);
    return $this->dao->get_by_id($id);
  }

  // UPDATE
  public function update($id, $data) {
    if (!$this->dao->get_by_id($id)) {
      throw new Exception("role not found");
    }

    parent::update($id, $data);
    return $this->dao->get_by_id($id);
  }

  // DELETE
  public function delete($id) {
    if (!$this->dao->get_by_id($id)) {
      throw new Exception("role not found");
    }

    return parent::delete($id);
  }
}
?>