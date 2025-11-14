<?php
require_once __DIR__ . '/../services/RoleService.php';

use OpenApi\Annotations as OA;

Flight::set('role_service', new RoleService());

/**
 * @OA\Get(
 *     path="/roles",
 *     tags={"Roles"},
 *     summary="Get all roles",
 *     @OA\Response(
 *         response=200,
 *         description="List of roles",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="admin")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /roles', function () {
    Flight::json(Flight::get('role_service')->getAll());
});

/**
 * @OA\Get(
 *     path="/roles/{id}",
 *     tags={"Roles"},
 *     summary="Get a role by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Role ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role found",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="admin")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found"
 *     )
 * )
 */
Flight::route('GET /roles/@id', function ($id) {
    Flight::json(Flight::get('role_service')->getById($id));
});

/**
 * @OA\Post(
 *     path="/roles",
 *     tags={"Roles"},
 *     summary="Create a new role",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="organizer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Created role",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="name", type="string")
 *         )
 *     )
 * )
 */
Flight::route('POST /roles', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('role_service')->create($data));
});

/**
 * @OA\Put(
 *     path="/roles/{id}",
 *     tags={"Roles"},
 *     summary="Update a role",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Role ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="updated-role")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated role",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string")
 *         )
 *     )
 * )
 */
Flight::route('PUT /roles/@id', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::get('role_service')->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/roles/{id}",
 *     tags={"Roles"},
 *     summary="Delete a role",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Role ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Role deleted"
 *     )
 * )
 */
Flight::route('DELETE /roles/@id', function ($id) {
    Flight::get('role_service')->delete($id);
    Flight::json(['message' => 'Role deleted successfully']);
});
?>