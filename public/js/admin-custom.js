$(document).ready(function () {
    function ajustarTablas() {
        if ($.fn.dataTable) {
            $.fn.dataTable
                .tables({ visible: true, api: true })
                .columns.adjust()
                .responsive?.recalc?.();
        }
    }

    $('[data-widget="pushmenu"]').on('click', function () {
        ajustarTablas();

        setTimeout(ajustarTablas, 80);
        setTimeout(ajustarTablas, 180);
        setTimeout(ajustarTablas, 300);
    });

    $(window).on('resize', function () {
        ajustarTablas();
        setTimeout(ajustarTablas, 100);
    });

    $('.main-sidebar').on('transitionend webkitTransitionEnd', function () {
        ajustarTablas();
    });
});