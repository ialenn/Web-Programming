<?php
require_once __DIR__ . '/../dao/RoleDao.php';
Flight::set('role_dao', new RoleDao());

// GET all roles
Flight::route('GET /roles', function() {
    Flight::json(Flight::get('role_dao')->get_all());
});

// GET role by ID
Flight::route('GET /roles/@id', function($id) {
    Flight::json(Flight::get('role_dao')->get_by_id($id));
});

// POST new role
Flight::route('POST /roles', function() {
    $data = Flight::request()->data->getData();
    $id = Flight::get('role_dao')->insert($data);
    Flight::json(['id' => $id]);
});

// PUT update role
Flight::route('PUT /roles/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::get('role_dao')->update($id, $data);
    Flight::json(['message' => 'Role updated']);
});

// DELETE role
Flight::route('DELETE /roles/@id', function($id) {
    Flight::get('role_dao')->delete($id);
    Flight::json(['message' => 'Role deleted']);
});
?>