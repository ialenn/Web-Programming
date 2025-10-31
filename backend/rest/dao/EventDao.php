<?php
require_once __DIR__ . '/BaseDao.php';

class EventDao extends BaseDao {
    public function __construct() {
        parent::__construct('events');
    }

    public function get_by_category($category_id) {
        $stmt = $this->connection->prepare("SELECT * FROM events WHERE category_id = :category_id");
        $stmt->execute(['category_id' => $category_id]);
        return $stmt->fetchAll();
    }

    public function get_by_venue($venue_id) {
        $stmt = $this->connection->prepare("SELECT * FROM events WHERE venue_id = :venue_id");
        $stmt->execute(['venue_id' => $venue_id]);
        return $stmt->fetchAll();
    }
}
?>