<?php
include_once '../db.php';
session_start();
checkAuth();

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $activeParkings = getParkingReservationsByUser($userId, true);
    $passedParkings = getParkingReservationsByUser($userId, false);

    echo json_encode([
        'success' => true,
        'active_parkings' => $activeParkings,
        'passed_parkings' => $passedParkings
    ]);
}
