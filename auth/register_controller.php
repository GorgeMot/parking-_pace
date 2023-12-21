<?php
include_once '../db.php';

header('Content-Type: application/json');

$login = trim($_POST['login'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirmPassword = trim($_POST['confirmPassword'] ?? '');
$carNumber = trim($_POST['carNumber'] ?? '');

if (
    empty($login) || empty($carNumber) || empty($password)
    || empty($confirmPassword) || !preg_match('/^[a-zA-Z0-9]+$/', $login)
    || !preg_match('/^[a-zA-Z0-9]+$/', $password)
) {
    echo json_encode(['success' => false, 'error' => 'Неверно заполены поля.']);
    exit;
}

if (strlen($login) < 4 || strlen($password) < 4) {
    echo json_encode(['success' => false, 'error' => 'Логин или пароль слишком маленькие.']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'error' => 'Пароли не совпадают.']);
    exit;
}

if (userExists($login)) {
    echo json_encode(['success' => false, 'error' => 'Пользователь с таким логином уже существует.']);
    exit;
}

if (carNumberExist($carNumber)) {
    echo json_encode(['success' => false, 'error' => 'Данный номер машины уже есть в системе.']);
    exit;
}

registerUser($login, $password, $carNumber);

echo json_encode(['success' => true]);
