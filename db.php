<?php
function dbConnect()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "wheelspay";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function carNumberExist($carNumber)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE car_number = ?");
    $stmt->bind_param("s", $carNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $exists;
}

function userExists($login)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $exists;
}

function registerUser($login, $password, $carNumber)
{
    $conn = dbConnect();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (login, password, car_number) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $login, $hashed_password, $carNumber);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function loginUser($login, $password)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $stmt->close();
            $conn->close();
            session_start();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["car_number"] = $user["car_number"];
            $_SESSION["login"] = $user["login"];
            return true;
        }
    }
    $stmt->close();
    $conn->close();
    return false;
}

function isAdmin()
{

    $userId = $_SESSION["user_id"];
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $isAdmin = $result->fetch_assoc()['admin'];
    $stmt->close();
    $conn->close();
    return $isAdmin;
}

function checkAuth()
{

    if (!isset($_SESSION["user_id"])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function checkNotAuth()
{

    if (isset($_SESSION["user_id"])) {
        header("Location: ../main/main.php");
        exit;
    }
}

function checkAdmin()
{
    if (!isAdmin()) {
        header("Location: ../main/main.php");
        exit;
    }
}

function getParkings()
{
    $conn = dbConnect();
    $sql = "SELECT * FROM parkings";
    $result = $conn->query($sql);

    $parkings = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $parkings[] = $row;
        }
    }

    $conn->close();
    return $parkings;
}

function addParking($address, $latitude, $longitude, $pricePerHour)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("INSERT INTO parkings (address, latitude, longitude, price_per_hour) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sddd", $address, $latitude, $longitude, $pricePerHour);
    $stmt->execute();

    $insertedId = $stmt->insert_id;

    $stmt->close();
    $conn->close();

    return $insertedId > 0 ? $insertedId : false;
}

function reserveParking($userId, $parkingId, $startTime, $endTime, $totalCost)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("INSERT INTO parking_reservations (user_id, parking_id, start_time, end_time, total_cost) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $userId, $parkingId, $startTime, $endTime, $totalCost);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function checkActiveReservation($userId, $parkingId)
{
    $conn = dbConnect();
    $sql = "SELECT id FROM parking_reservations WHERE user_id = ? AND parking_id = ? AND end_time > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $parkingId);
    $stmt->execute();
    $result = $stmt->get_result();
    $hasActiveReservation = $result->num_rows > 0;
    $stmt->close();
    $conn->close();

    return $hasActiveReservation;
}

function deleteParking($parkingId)
{
    $conn = dbConnect();
    $sql = "DELETE FROM parkings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $parkingId);
    $stmt->execute();
    $isDeleted = $stmt->affected_rows > 0;
    $stmt->close();
    $conn->close();

    return $isDeleted;
}

function getParkingReservationsByUser($userId, $isActive)
{
    $conn = dbConnect();
    $sql = $isActive
        ? "SELECT pr.*, p.address, p.latitude, p.longitude FROM parking_reservations pr INNER JOIN parkings p ON pr.parking_id = p.id WHERE pr.user_id = ? AND pr.end_time > NOW()"
        : "SELECT pr.*, p.address, p.latitude, p.longitude FROM parking_reservations pr INNER JOIN parkings p ON pr.parking_id = p.id WHERE pr.user_id = ? AND pr.end_time <= NOW()";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $parkings = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();

    return $parkings;
}
