<?php
require_once __DIR__ . '/BaseDao.php';

class RoleDao extends BaseDao {
    public function __construct() {
        parent::__construct('roles');
    }
}
?>