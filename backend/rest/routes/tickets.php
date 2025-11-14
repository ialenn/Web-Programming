<?php
require_once __DIR__ . '/../services/TicketService.php';

use OpenApi\Annotations as OA;

Flight::set('ticket_service', new TicketService());

/**
 * @OA\Get(
 *     path="/tickets",
 *     tags={"Tickets"},
 *     summary="Get all tickets",
 *     @OA\Response(
 *         response=200,
 *         description="List of tickets",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="event_id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="price", type="number", format="float", example=25.5)
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /tickets', function () {
    Flight::json(Flight::get('ticket_service')->getAll());
});

/**
 * @OA\Get(
 *     path="/tickets/{id}",
 *     tags={"Tickets"},
 *     summary="Get a ticket by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Ticket ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ticket found",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="event_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="price", type="number", format="float", example=25.5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Ticket not found"
 *     )
 * )
 */
Flight::route('GET /tickets/@id', function ($id) {
    Flight::json(Flight::get('ticket_service')->getById($id));
});

/**
 * @OA\Post(
 *     path="/tickets",
 *     tags={"Tickets"},
 *     summary="Create a new ticket",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"event_id","user_id","price"},
 *             @OA\Property(property="event_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="price", type="number", format="float", example=25.5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Created ticket",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="event_id", type="integer"),
 *             @OA\Property(property="user_id", type="integer"),
 *             @OA\Property(property="price", type="number", format="float")
 *         )
 *     )
 * )
 */
Flight::route('POST /tickets', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('ticket_service')->create($data));
});

/**
 * @OA\Put(
 *     path="/tickets/{id}",
 *     tags={"Tickets"},
 *     summary="Update a ticket",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Ticket ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="event_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="price", type="number", format="float", example=30.0)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated ticket",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="event_id", type="integer"),
 *             @OA\Property(property="user_id", type="integer"),
 *             @OA\Property(property="price", type="number", format="float")
 *         )
 *     )
 * )
 */
Flight::route('PUT /tickets/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('ticket_service')->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/tickets/{id}",
 *     tags={"Tickets"},
 *     summary="Delete a ticket",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Ticket ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ticket deleted"
 *     )
 * )
 */
Flight::route('DELETE /tickets/@id', function ($id) {
    Flight::get('ticket_service')->delete($id);
    Flight::json(['message' => 'Ticket deleted successfully']);
});
?>