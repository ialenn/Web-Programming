<?php
require_once __DIR__ . '/../services/EventService.php';

use OpenApi\Annotations as OA;

Flight::set('event_service', new EventService());

/**
 * @OA\Get(
 *     path="/events",
 *     tags={"Events"},
 *     summary="Get all events",
 *     @OA\Response(
 *         response=200,
 *         description="List of events",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Tech Conference"),
 *                 @OA\Property(property="description", type="string", example="Annual IT conference"),
 *                 @OA\Property(property="starts_at", type="string", format="date-time", example="2025-10-20 10:00:00"),
 *                 @OA\Property(property="ends_at", type="string", format="date-time", example="2025-10-20 18:00:00"),
 *                 @OA\Property(property="venue_id", type="integer", example=1),
 *                 @OA\Property(property="category_id", type="integer", example=2)
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /events', function () {
    Flight::json(Flight::get('event_service')->getAll());
});

/**
 * @OA\Get(
 *     path="/events/{id}",
 *     tags={"Events"},
 *     summary="Get a single event by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Event ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Event found",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Tech Conference"),
 *             @OA\Property(property="description", type="string", example="Annual IT conference"),
 *             @OA\Property(property="starts_at", type="string", format="date-time"),
 *             @OA\Property(property="ends_at", type="string", format="date-time"),
 *             @OA\Property(property="venue_id", type="integer"),
 *             @OA\Property(property="category_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Event not found"
 *     )
 * )
 */
Flight::route('GET /events/@id', function ($id) {
    Flight::json(Flight::get('event_service')->getById($id));
});

/**
 * @OA\Post(
 *     path="/events",
 *     tags={"Events"},
 *     summary="Create a new event",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","starts_at","venue_id"},
 *             @OA\Property(property="title", type="string", example="Tech Conference"),
 *             @OA\Property(property="description", type="string", example="Annual IT conference"),
 *             @OA\Property(property="starts_at", type="string", format="date-time", example="2025-10-20 10:00:00"),
 *             @OA\Property(property="ends_at", type="string", format="date-time", example="2025-10-20 18:00:00"),
 *             @OA\Property(property="venue_id", type="integer", example=1),
 *             @OA\Property(property="category_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Created event",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="starts_at", type="string", format="date-time"),
 *             @OA\Property(property="ends_at", type="string", format="date-time"),
 *             @OA\Property(property="venue_id", type="integer"),
 *             @OA\Property(property="category_id", type="integer")
 *         )
 *     )
 * )
 */
Flight::route('POST /events', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('event_service')->create($data));
});

/**
 * @OA\Put(
 *     path="/events/{id}",
 *     tags={"Events"},
 *     summary="Update an event",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Event ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Updated Conference"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="starts_at", type="string", format="date-time"),
 *             @OA\Property(property="ends_at", type="string", format="date-time"),
 *             @OA\Property(property="venue_id", type="integer"),
 *             @OA\Property(property="category_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated event",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="starts_at", type="string", format="date-time"),
 *             @OA\Property(property="ends_at", type="string", format="date-time"),
 *             @OA\Property(property="venue_id", type="integer"),
 *             @OA\Property(property="category_id", type="integer")
 *         )
 *     )
 * )
 */
Flight::route('PUT /events/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('event_service')->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/events/{id}",
 *     tags={"Events"},
 *     summary="Delete an event",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Event ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Event deleted"
 *     )
 * )
 */
Flight::route('DELETE /events/@id', function ($id) {
    Flight::get('event_service')->delete($id);
    Flight::json(['message' => 'Event deleted successfully']);
});
?>