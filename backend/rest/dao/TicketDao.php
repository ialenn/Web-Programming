<?php
require_once __DIR__ . '/BaseDao.php';

class TicketDao extends BaseDao {
    public function __construct() {
        parent::__construct('tickets');
    }
}
?>