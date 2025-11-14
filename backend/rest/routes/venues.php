<?php
require_once __DIR__ . '/../services/VenueService.php';

use OpenApi\Annotations as OA;

Flight::set('venue_service', new VenueService());

/**
 * @OA\Get(
 *     path="/venues",
 *     tags={"Venues"},
 *     summary="Get all venues",
 *     @OA\Response(
 *         response=200,
 *         description="List of venues",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="City Hall"),
 *                 @OA\Property(property="address", type="string", example="Obala Kulina bana 1"),
 *                 @OA\Property(property="capacity", type="integer", example=600)
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /venues', function () {
    Flight::json(Flight::get('venue_service')->getAll());
});

/**
 * @OA\Get(
 *     path="/venues/{id}",
 *     tags={"Venues"},
 *     summary="Get a single venue by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Venue ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Venue found",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="City Hall"),
 *             @OA\Property(property="address", type="string", example="Obala Kulina bana 1"),
 *             @OA\Property(property="capacity", type="integer", example=600)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Venue not found"
 *     )
 * )
 */
Flight::route('GET /venues/@id', function ($id) {
    Flight::json(Flight::get('venue_service')->getById($id));
});

/**
 * @OA\Post(
 *     path="/venues",
 *     tags={"Venues"},
 *     summary="Create a new venue",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","address","capacity"},
 *             @OA\Property(property="name", type="string", example="Skenderija"),
 *             @OA\Property(property="address", type="string", example="Terezija bb"),
 *             @OA\Property(property="capacity", type="integer", example=3000)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Created venue",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="address", type="string"),
 *             @OA\Property(property="capacity", type="integer")
 *         )
 *     )
 * )
 */
Flight::route('POST /venues', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('venue_service')->create($data));
});

/**
 * @OA\Put(
 *     path="/venues/{id}",
 *     tags={"Venues"},
 *     summary="Update an existing venue",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Venue ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated City Hall"),
 *             @OA\Property(property="address", type="string", example="Updated address"),
 *             @OA\Property(property="capacity", type="integer", example=5000)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated venue",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="address", type="string"),
 *             @OA\Property(property="capacity", type="integer")
 *         )
 *     )
 * )
 */
Flight::route('PUT /venues/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('venue_service')->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/venues/{id}",
 *     tags={"Venues"},
 *     summary="Delete a venue",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Venue ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Venue deleted"
 *     )
 * )
 */
Flight::route('DELETE /venues/@id', function ($id) {
    Flight::get('venue_service')->delete($id);
    Flight::json(['message' => 'Venue deleted successfully']);
});
?>