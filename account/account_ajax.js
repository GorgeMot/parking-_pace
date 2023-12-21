function loadParkings() {
    $.ajax({
        url: 'account_mediator.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                displayParkings(response.active_parkings, '#active-parkings');
                displayParkings(response.passed_parkings, '#passed-parkings');
            } else {
                alert(response.error);
            }
        },
        error: function () {
            alert('Ошибка при загрузке данных о парковках.');
        }
    });
}

function displayParkings(parkings, containerSelector) {
    var container = $(containerSelector);
    var existingParkingIds = container.find('.card').map(function () {
        return $(this).data('parkingId');
    }).get();

    container.find('.card').each(function () {
        var card = $(this);
        if (!parkings.some(p => p.id === card.data('parkingId'))) {
            card.remove();
        }
    });

    if (parkings.length === 0) {
        container.html('<p id="empty-parkings">У вас нету парковок по этому фильтру!</p>');
        return;
    } else {
        container.find('#empty-parkings').remove();
    }

    parkings.forEach(parking => {
        if (!existingParkingIds.includes(parking.id)) {
            var parkingCard = $('<div class="card" data-parking-id="' + parking.id + '"></div>');
            parkingCard.append(`<h5>${parking.address}</h5>`);
            parkingCard.append(`<p>Начало: ${parking.start_time}</p>`);
            parkingCard.append(`<p>Конец: ${parking.end_time}</p>`);
            parkingCard.append(`<p>Стоимость: ${parking.total_cost}₽</p>`);

            var mapContainer = $('<div id="parking-map-' + parking.id + '" style="height: 150px;"></div>');
            parkingCard.append(mapContainer);

            container.append(parkingCard);

            ymaps.ready(function () {
                var parkingMap = new ymaps.Map('parking-map-' + parking.id, {
                    center: [parking.latitude, parking.longitude],
                    zoom: 14
                });
                parkingMap.geoObjects.add(new ymaps.Placemark([parking.latitude, parking.longitude]));
            });
        }
    });
}

setInterval(loadParkings, 5000);
loadParkings();
