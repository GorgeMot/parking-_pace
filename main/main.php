<?php
include_once '../db.php';
session_start();
checkAuth();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Онлайн Парковки WheelsPay</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=a2417be9-a275-41c3-9211-945cc695832e&lang=ru_RU" type="text/javascript"></script>
</head>

<body>
    <header class="navbar navbar-expand-lg navbar-light bg-light justify-content-between">
        <a href="../main/main.php" class="navbar-brand" style="text-decoration: underline;">Парковки</a>
        <a href="../account/account.php" class="navbar-brand">Личный кабинет</a>
        <a href="../auth/logout.php" class="navbar-brand">Выйти</a>
    </header>

    <div class="d-flex justify-content-center mt-2">
        <h2>Добро пожаловать на систему городских парковок Санкт-Петербурга WheelsPay!</h2>
    </div>

    <div id="search-container" class="container rounded p-3 my-3">
        <!-- Контент для оформления парковки -->
    </div>

    <div id="map" style="width: 100%; height: 70vh;"></div>

    <?php if (isAdmin()) : ?>
        <!-- Модальное окно для добавления новой парковки -->
        <div id="add-parking-modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Добавить новое место для парковки</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new-parking-address">Адрес парковки:</label>
                            <input type="text" id="new-parking-address" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="new-parking-price-per-hour">Цена за час:</label>
                            <input type="number" id="new-parking-price-per-hour" class="form-control" min="1" step="0.01" placeholder="Цена за час">
                        </div>
                        <input type="hidden" id="new-parking-latitude">
                        <input type="hidden" id="new-parking-longitude">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="submit-new-parking">Добавить</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.1/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="main_ajax.js" defer></script>
</body>

</html>
