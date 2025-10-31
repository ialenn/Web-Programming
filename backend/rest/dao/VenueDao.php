<?php
require_once __DIR__ . '/BaseDao.php';

class VenueDao extends BaseDao {
    public function __construct() {
        parent::__construct('venues');
    }
}
?>