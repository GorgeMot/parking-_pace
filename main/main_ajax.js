ymaps.ready(init);
function init() {
    var isAdmin = false;

    var parkingMarkers = {};

    var myMap = new ymaps.Map("map", {
        center: [59.9342802, 30.3350986],
        zoom: 12,
        controls: ['zoomControl', 'searchControl', 'typeSelector', 'fullscreenControl']
    });

    var searchControl = myMap.controls.get('searchControl');
    searchControl.options.set('boundedBy', [[59.7000, 29.3000], [60.1000, 30.6000]]);

    myMap.events.add('contextmenu', function (e) {
        var coords = e.get('coords');
        ymaps.geocode(coords).then(function (res) {
            var firstGeoObject = res.geoObjects.get(0);
            var address = firstGeoObject.getAddressLine();

            $('#new-parking-address').val(address);
            $('#new-parking-latitude').val(coords[0]);
            $('#new-parking-longitude').val(coords[1]);
            $('#add-parking-modal').modal('show');
        });
    });


    $('#add-parking-button').on('click', function () {
        $('#add-parking-modal').modal('show');

    });

    $('#submit-new-parking').on('click', function () {
        var address = $('#new-parking-address').val();
        var latitude = $('#new-parking-latitude').val();
        var longitude = $('#new-parking-longitude').val();
        var pricePerHour = $('#new-parking-price-per-hour').val();

        $.ajax({
            url: 'main_mediator.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'addParking',
                address: address,
                latitude: latitude,
                longitude: longitude,
                pricePerHour: pricePerHour
            },
            success: function (response) {
                if (response.success) {
                    alert('Новая парковка успешно добавлена.');
                    $('#add-parking-modal').modal('hide');
                    $('#new-parking-price-per-hour').val("");
                    addParkingMarker({
                        price_per_hour: pricePerHour,
                        address: address,
                        latitude: latitude,
                        longitude: longitude,
                        id: response.id,
                    });
                } else {
                    alert(response.error);
                }
            },
            error: function () {
                alert('Ошибка при выполнении запроса.');
            }
        });
    });

    function showParkingInfo(parkingData) {
        $('#search-container').html(`
                <h3>Адрес: ${parkingData.address}</h3>
                <p>Цена за час: ${parkingData.price_per_hour} ₽</p>
                <input type="number" id="parking-duration" min="1" class="form-control" placeholder="Количество часов">
                <button id="confirm-parking" class="btn btn-primary mt-2">Оформить парковку</button>
                ${isAdmin ? '<button id="delete-parking" class="btn btn-danger mt-2">Удалить парковку</button>' : ''}
            `);

        $('#confirm-parking').on('click', function () {
            var duration = parseInt($('#parking-duration').val());

            if (isNaN(duration)) {
                alert('Укажите количество часов!');
                return;
            }

            var parkingId = parkingData.id;
            var startTime = new Date();
            startTime.setHours(startTime.getHours() + 3);

            var endTime = new Date(startTime.getTime());
            endTime.setHours(endTime.getHours() + duration);

            var totalCost = parkingData.price_per_hour * duration;

            $.ajax({
                url: 'main_mediator.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'reserveParking',
                    parkingId: parkingId,
                    startTime: startTime.toISOString(),
                    endTime: endTime.toISOString(),
                    totalCost: totalCost
                },
                success: function (response) {
                    if (response.success) {
                        alert('Парковка успешно оформлена.');
                        window.location.href = "../account/account.php";
                    } else {
                        alert(response.error);
                    }
                },
                error: function () {
                    alert('Ошибка при выполнении запроса.');
                }
            });
        });

        $('#delete-parking').on('click', function () {
            var parkingId = parkingData.id;

            $.ajax({
                url: 'main_mediator.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'deleteParking',
                    parkingId: parkingId
                },
                success: function (response) {
                    if (response.success) {
                        alert('Парковка успешно удалена.');
                        if (parkingMarkers[parkingId]) {
                            myMap.geoObjects.remove(parkingMarkers[parkingId]);
                            delete parkingMarkers[parkingId];
                        }
                        $('#search-container').empty();
                    } else {
                        alert(response.error);
                    }
                },
                error: function () {
                    alert('Ошибка при выполнении запроса.');
                }
            });
        });

    }

    function addParkingMarker(parking) {
        var marker = new ymaps.Placemark([parking.latitude, parking.longitude], {
            balloonContent: `<strong>${parking.address}</strong><br>Цена: ${parking.price_per_hour} ₽/час`
        });
        myMap.geoObjects.add(marker);
        marker.events.add('click', function () {
            showParkingInfo(parking);
        });
        parkingMarkers[parking.id] = marker;
    }

    function loadParkings() {
        $.ajax({
            url: 'main_mediator.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    isAdmin = response.isAdmin;
                    response.data.forEach(function (parking) {
                        addParkingMarker(parking);
                    });
                } else {
                    alert(response.error);
                }
            },
            error: function () {
                alert('Ошибка при загрузке данных о парковках.');
            }
        });
    }

    loadParkings();
}
