<?php
include_once '../db.php';

header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $parkings = getParkings();
    echo json_encode(['success' => true, 'data' => $parkings, 'isAdmin' => isAdmin()]);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addParking') {
    if (!isAdmin()) {
        echo json_encode(['success' => false, 'error' => 'Недостаточно прав']);
        exit;
    }

    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $pricePerHour = $_POST['pricePerHour'];
    $newId = addParking($address, $latitude, $longitude, $pricePerHour);
    if ($newId) {
        echo json_encode(['success' => true, 'id' => $newId]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Не удалось добавить парковку']);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reserveParking') {
    $userId = $_SESSION['user_id'];
    $parkingId = $_POST['parkingId'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $totalCost = $_POST['totalCost'];

    if (checkActiveReservation($userId, $parkingId)) {
        echo json_encode(['success' => false, 'error' => 'У вас уже есть активное бронирование на это место']);
        exit;
    }

    $result = reserveParking($userId, $parkingId, $startTime, $endTime, $totalCost);

    if ($result) {
        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Не удалось оформить парковку']);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteParking') {
    if (!isAdmin()) {
        echo json_encode(['success' => false, 'error' => 'Недостаточно прав для выполнения операции']);
        exit;
    }

    $parkingId = $_POST['parkingId'];

    if (deleteParking($parkingId)) {
        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Не удалось удалить парковку']);
        exit;
    }
}
