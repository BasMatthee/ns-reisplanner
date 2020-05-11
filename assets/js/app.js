require('daemonite-material');

$(document).ready(function () {
    $('select#station_station_code').on('change', function (event) {
        $(this).closest('form').submit();
    });
});
