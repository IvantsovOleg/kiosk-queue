$(function () {

    var $activeInput;

    // Получаем все поля формы
    var inputFields = $("form#user-auth-form input[type=text]");

    // Получаем первый элемент формы
    var inputFieldFirst = inputFields.first();

    // Добавляем к выбранному активному элементу класс
    inputFields.focus(function () {
        $("form#user-auth-form .form-group").removeClass("active");
        $activeInput = $(this);
        $activeInput.parent().parent().addClass("active");
    });

    // При открытии инициализируем первый элемент формы
    inputFieldFirst.focus();

    $("#keyboard div.key").click(function () {

        var $this = $(this);
        var character = $this.html();

        // Стереть один символ
        if ($this.hasClass('delete')) {
            var value = $activeInput.val();
            $activeInput.val(value.substr(0, value.length - 1));
            return false;
        }

        // Очистить все поля
        if ($this.hasClass('clear')) {
            inputFields.each(function () {
                $(this).val('');
            });
            inputFieldFirst.focus();
            return false;
        }

        // Следующее поле
        if ($this.hasClass('next-field')) {
            var index = inputFields.index($activeInput);
            var next = inputFields.eq(index + 1).length ? inputFields.eq(index + 1) : inputFields.eq(0);
            next.focus();
            return false;
        }

        // Спец символы
        if ($this.hasClass('symbol')) character = $this.html();
        if ($this.hasClass('space')) character = ' ';

        // Добавить значение
        $activeInput.val($activeInput.val() + character).trigger('input');
        $activeInput.focus();
    });
});