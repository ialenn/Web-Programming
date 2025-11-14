<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TicketDao.php';
require_once __DIR__ . '/../dao/EventDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class TicketService extends BaseService {
  private $eventDao;
  private $userDao;

  public function __construct() {
    parent::__construct(new TicketDao());
    $this->eventDao = new EventDao();
    $this->userDao  = new UserDao();
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
    if (empty($data['event_id'])) throw new Exception("event_id is required");
    if (empty($data['user_id']))  throw new Exception("user_id is required");
    if (empty($data['price']))    throw new Exception("price is required");

    if (!$this->eventDao->get_by_id($data['event_id'])) {
      throw new Exception("event not found");
    }
    if (!$this->userDao->get_by_id($data['user_id'])) {
      throw new Exception("user not found");
    }

    $id = parent::create($data);
    return $this->dao->get_by_id($id);
  }

  // UPDATE
  public function update($id, $data) {
    if (!$this->dao->get_by_id($id)) {
      throw new Exception("ticket not found");
    }

    parent::update($id, $data);
    return $this->dao->get_by_id($id);
  }

  // DELETE
  public function delete($id) {
    if (!$this->dao->get_by_id($id)) {
      throw new Exception("ticket not found");
    }

    return parent::delete($id);
  }
}
?>