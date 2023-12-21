<?php
include_once '../db.php';
session_start();
checkAuth();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Личный Кабинет <?php echo $_SESSION["login"] ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=a2417be9-a275-41c3-9211-945cc695832e&lang=ru_RU" type="text/javascript"></script>
    <style>
        hr.rounded {
            border-top: 8px solid #bbb;
            border-radius: 5px;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .card {
            width: calc(50% - 10px);
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
    </style>
</head>

<body>
    <header class="navbar navbar-expand-lg navbar-light bg-light justify-content-between">
        <a href="../main/main.php" class="navbar-brand">Парковки</a>
        <a href="../account/account.php" class="navbar-brand" style="text-decoration: underline;">Личный кабинет</a>
        <a href="../auth/logout.php" class="navbar-brand">Выйти</a>
    </header>

    <div class="container mt-4">
        <h2>Привет, <span id="username"><?php echo $_SESSION["login"] ?></span>!</h2>
        <h4>Тут будет список твоих парковок по автомобилю с номером <span id="carNumber"><?php echo $_SESSION["car_number"] ?></span>.</h4>

        <hr class="rounded">

        <h3>Активные парковки</h3>
        <div id="active-parkings" class="parkings card-container">
            <!-- Карточки активных парковок будут здесь -->
        </div>

        <hr class="rounded">

        <h3>Прошедшие парковки</h3>
        <div id="passed-parkings" class="parkings card-container">
            <!-- Карточки прошедших парковок будут здесь -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.1/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="account_ajax.js" defer></script>
</body>

</html>
