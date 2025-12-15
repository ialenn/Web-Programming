<?php
// define the authentication routes they don't require authentication middleware they are used to obtain the JWT token
require_once __DIR__ . '/../services/AuthService.php';

use OpenApi\Annotations as OA;

Flight::set('auth_service', new AuthService());

/**
 * @OA\Post(
 *     path="/auth/register",
 *     tags={"Auth"},
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Registered user"
 *     )
 * )
 */
Flight::route('POST /auth/register', function () {

    $data = Flight::request()->data->getData();

    if (!$data || !is_array($data) || count($data) === 0) {
        $raw_body = Flight::request()->getBody();
        if ($raw_body) {
            $json_data = json_decode($raw_body, true);
            if (is_array($json_data)) {
                $data = $json_data;
            }
        }
    }

    $result = Flight::get('auth_service')->register($data);

    if ($result['success']) {
        Flight::json(array(
            "success" => true,
            "token"   => null,
            "user"    => $result["user"]
        ));
    } else {
        Flight::halt(400, $result['error']);
    }
});

/**
 * @OA\Post(
 *     path="/auth/login",
 *     tags={"Auth"},
 *     summary="Login user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Logged in user + token"
 *     )
 * )
 */
Flight::route('POST /auth/login', function () {

    $data = Flight::request()->data->getData();

    if (!$data || !is_array($data) || count($data) === 0) {
        $raw_body = Flight::request()->getBody();
        if ($raw_body) {
            $json_data = json_decode($raw_body, true);
            if (is_array($json_data)) {
                $data = $json_data;
            }
        }
    }

    $result = Flight::get('auth_service')->login($data);

    if ($result['success']) {
        Flight::json($result);
    } else {
        Flight::halt(401, $result['error']);
    }
});