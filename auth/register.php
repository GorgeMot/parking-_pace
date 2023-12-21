<?php
include_once '../db.php';
session_start();
checkNotAuth();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-md-offset-3">
                <h2 class="text-center">Регистрация</h2>
                <form id="registerForm">
                    <div class="form-group">
                        <label for="carNumber">Номер машины</label>
                        <input type="text" class="form-control" id="carNumber" pattern="[АВЕКМНОРСТУХавекмнорстух][0-9]{3}[АВЕКМНОРСТУХавекмнорстух]{2}[0-9]{2,3}" required>
                        <small class="form-text text-muted">Формат: А111АА(77)</small>
                    </div>
                    <div class="form-group">
                        <label for="login">Логин</label>
                        <input type="text" class="form-control" id="login" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Подтверждение пароля</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                </form>
                <a href="login.php">Авторизация</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                var login = $('#login').val();
                var carNumber = $('#carNumber').val();
                var password = $('#password').val();
                var confirmPassword = $('#confirmPassword').val();

                var carNumberRegex = /^[АВЕКМНОРСТУХавекмнорстух][0-9]{3}[АВЕКМНОРСТУХавекмнорстух]{2}[0-9]{2,3}$/;
                if (!carNumberRegex.test(carNumber)) {
                    alert("Некорректный номер машины. Пожалуйста, проверьте формат.");
                    return;
                }

                $.ajax({
                    url: 'register_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        login: login,
                        password: password,
                        confirmPassword: confirmPassword,
                        carNumber: carNumber,
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'login.php';
                        } else {
                            alert(response.error);
                        }
                    }
                });
            });
        });
    </script>

</body>

</html>
