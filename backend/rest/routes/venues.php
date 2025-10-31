<?php
require_once __DIR__ . '/../dao/VenueDao.php';
Flight::set('venue_dao', new VenueDao());

// GET all venues
Flight::route('GET /venues', function() {
    Flight::json(Flight::get('venue_dao')->get_all());
});

// GET venue by ID
Flight::route('GET /venues/@id', function($id) {
    Flight::json(Flight::get('venue_dao')->get_by_id($id));
});

// POST new venue
Flight::route('POST /venues', function() {
    $data = Flight::request()->data->getData();
    $id = Flight::get('venue_dao')->insert($data);
    Flight::json(['id' => $id]);
});

// PUT update venue
Flight::route('PUT /venues/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::get('venue_dao')->update($id, $data);
    Flight::json(['message' => 'Venue updated']);
});

// DELETE venue
Flight::route('DELETE /venues/@id', function($id) {
    Flight::get('venue_dao')->delete($id);
    Flight::json(['message' => 'Venue deleted']);
});
?>