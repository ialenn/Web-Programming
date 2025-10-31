<?php
require_once __DIR__ . '/../dao/EventDao.php';

Flight::set('event_dao', new EventDao());

// GET all events
Flight::route('GET /events', function() {
    Flight::json(Flight::get('event_dao')->get_all());
});

// GET one event by ID
Flight::route('GET /events/@id', function($id) {
    Flight::json(Flight::get('event_dao')->get_by_id($id));
});

// POST create new event
Flight::route('POST /events', function() {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('event_dao')->insert($data));
});

// PUT update event
Flight::route('PUT /events/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('event_dao')->update($id, $data));
});

// DELETE event
Flight::route('DELETE /events/@id', function($id) {
    Flight::get('event_dao')->delete($id);
    Flight::json(['message' => 'Event deleted successfully']);
});
?>