<?php
require_once __DIR__ . '/../dao/UserDao.php';
Flight::set('user_dao', new UserDao());

// GET all users
Flight::route('GET /users', function() {
    Flight::json(Flight::get('user_dao')->get_all());
});

// GET user by ID
Flight::route('GET /users/@id', function($id) {
    Flight::json(Flight::get('user_dao')->get_by_id($id));
});

// POST new user
Flight::route('POST /users', function() {
    $data = Flight::request()->data->getData();
    $id = Flight::get('user_dao')->insert($data);
    Flight::json(['id' => $id]);
});

// PUT update user
Flight::route('PUT /users/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::get('user_dao')->update($id, $data);
    Flight::json(['message' => 'User updated']);
});

// DELETE user
Flight::route('DELETE /users/@id', function($id) {
    Flight::get('user_dao')->delete($id);
    Flight::json(['message' => 'User deleted']);
});
?>