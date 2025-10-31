<?php

require_once __DIR__ . '/dao/UserDao.php';
require_once __DIR__ . '/dao/EventDao.php';
require_once __DIR__ . '/dao/VenueDao.php';
require_once __DIR__ . '/dao/TicketDao.php';
require_once __DIR__ . '/dao/RoleDao.php';

$userDao   = new UserDao();
$eventDao  = new EventDao();
$venueDao  = new VenueDao();
$ticketDao = new TicketDao();
$roleDao   = new RoleDao();

echo "<pre>";

$users  = $userDao->get_all();   
print_r($users);

$events = $eventDao->get_all();
print_r($events);

$venues = $venueDao->get_all();
print_r($venues);

$tickets = $ticketDao->get_all();
print_r($tickets);

$roles = $roleDao->get_all();
print_r($roles);

echo "</pre>";
?>