$(document).ready(function () {
    var mySwiper = new Swiper('.swiper-box', {
        // Optional parameters
        direction: 'vertical',
        loop: false,
        spaceBetween: 30,
        slidesPerView: 5,
        slidesPerGroup: 4,

        // Navigation arrows
        navigation: {
            nextEl: '.button-next',
            prevEl: '.button-prev'
        }
    });

    $('#home').on('click', function () {
        if ('rnumbId' in sessionStorage) {
            $.ajax({
                type: 'POST',
                url: window.location.href = window.location.pathname + '?r=app%2Funset-block-rnumb-when-go-home',
                data: {'rnumbId': sessionStorage.getItem('rnumbId'),},
            }).done(function(data) {
                if (data.success) {
                    console.log('С номерка была успешно снята блокировка');
                } else {
                    console.log('С номерка не удалось снять блокировку');
                }
            }).fail(function() {
                console.log('Ajax запрос не прошел');
            });
        }
        sessionStorage.clear();
        localStorage.clear();
        window.location.href = window.location.pathname;
    });

    $('#back').on('click', function (e) {
        // Если это возврат то при переходе с choose-type-auth на страницу ранее - необходимо снять блокировку номерка
        if (window.location.href.includes('choose-type-auth') && window.location.href.includes('rnumbId')){
            $.ajax({
                type: 'POST',
                url: window.location.href,
            }).done(function(data) {
                if (data.success) {
                    console.log('С номерка была успешно снята блокировка');
                } else {
                    console.log('С номерка не удалось снять блокировку');
                }
            }).fail(function() {
                    sessionStorage.clear();
                    localStorage.clear();
                    window.location.href = window.location.pathname;
                    console.log('Ajax запрос к серверу не прошел. Приложение полностью сбросило все параметры SessionStorage и вернулось на главную страницу');
            });
            sessionStorage.removeItem('rnumbId');
        }
        window.history.back();
    });
});
