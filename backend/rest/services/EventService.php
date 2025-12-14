<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/EventDao.php';

class EventService extends BaseService {

  public function __construct() {
    parent::__construct(new EventDao());
  }

  public function get_all() {
    return $this->dao->get_all();
  }

  public function get_by_id($id) {
    return $this->dao->get_by_id($id);
  }

  public function get_by_category($category_id) {
    return $this->dao->get_by_category($category_id);
  }

  public function get_by_venue($venue_id) {
    return $this->dao->get_by_venue($venue_id);
  }

public function create($data) {
  if (empty($data['title']))     throw new Exception("title is required");
  if (empty($data['starts_at'])) throw new Exception("starts_at is required");
  if (empty($data['venue_id']))  throw new Exception("venue_id is required");

  if (!empty($data['starts_at'])) {
      $data['starts_at'] = str_replace("T", " ", $data['starts_at']) . ":00";
  }

  if (!empty($data['ends_at'])) {
      $data['ends_at'] = str_replace("T", " ", $data['ends_at']) . ":00";
  }

  return parent::create($data);
}

  // UPDATE
  public function update($id, $data) {
    if (!$this->dao->get_by_id($id)) {
      throw new Exception("event not found");
    }
    return parent::update($id, $data);
  }

  // DELETE
  public function delete($id) {
    if (!$this->dao->get_by_id($id)) {
      throw new Exception("event not found");
    }
    return parent::delete($id);
  }
}
?>