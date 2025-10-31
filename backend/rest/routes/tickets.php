<?php
require_once __DIR__ . '/../dao/TicketDao.php';
Flight::set('ticket_dao', new TicketDao());

// GET all tickets
Flight::route('GET /tickets', function() {
    Flight::json(Flight::get('ticket_dao')->get_all());
});

// GET ticket by ID
Flight::route('GET /tickets/@id', function($id) {
    Flight::json(Flight::get('ticket_dao')->get_by_id($id));
});

// POST new ticket
Flight::route('POST /tickets', function() {
    $data = Flight::request()->data->getData();
    $id = Flight::get('ticket_dao')->insert($data);
    Flight::json(['id' => $id]);
});

// PUT update ticket
Flight::route('PUT /tickets/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::get('ticket_dao')->update($id, $data);
    Flight::json(['message' => 'Ticket updated']);
});

// DELETE ticket
Flight::route('DELETE /tickets/@id', function($id) {
    Flight::get('ticket_dao')->delete($id);
    Flight::json(['message' => 'Ticket deleted']);
});
?>