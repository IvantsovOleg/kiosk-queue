<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<?php if (isset($_SESSION['patientChoice'])) {
    echo '<h2 style="padding-top: 1vh;" class="text-center" id="patientChoice">Ваш выбор: ' . mb_strtoupper($_SESSION['patientChoice']['specName']) . ' ' . $_SESSION['patientChoice']['docName'] . '<br>' . 'Время приема: ' . $_SESSION['patientChoice']['rnumbInfo'] . '</h2>';
} else {echo '&nbsp';}?>
    <h3 class="text-center" style="margin-bottom: 2vh" id="patientChoice">Пожалуйста, авторизуйтесь</h3>
    <div class="auth-container">
        <div class="auth-form">
            <?php $form = ActiveForm::begin( [
                'id'          => 'user-auth-form',
                'options'     => [ 'class' => 'form-horizontal' ],
                'fieldConfig' => [
                    'template'     => "{label}\n<div class=\"col-sm-9\">{input}</div>",
                    'labelOptions' => [ 'class' => 'col-sm-3 control-label' ],
                ],
            ] ) ?>
            <?= $form->field( $model, 'police_number' )->label( 'Номер полиса:*' ) ?>
            <?php ActiveForm::end(); ?>

        </div>

        <div class="keyboard-container num-keyboard">
            <?= $this->render( '_keyboard-num' ); ?>
        </div>
    </div>

<?php

$footer = Html::a( 'Отмена записи', Url::to( ['site/index'] ), ['id' => 'cancel-appt', 'class' => 'btn btn-danger' ] );
$footer .= '<button type="button" class="btn btn-default" data-dismiss="modal">Повторить ввод</button>';

yii\bootstrap\Modal::begin( [
    'header' => 'Ошибка авторизации',
    'footer' => $footer,
    'id'     => 'modal-user',
    'size'   => 'modal-lg',
] );
?>
    <div id='modal-content'>
        <h3 class="text-center">
            Загрузка...
        </h3>
    </div>


<?php yii\bootstrap\Modal::end(); ?>

<?php

if (Yii::$app->session['action'] == 'cancel'){
    $successUrl = Url::to(['cancel/index']);
} else {
    $successUrl = Url::to(['record/success']);
}

$js = <<<JS
$(document).ready(function () {
 //   $("#dob").mask("99.99.9999");
   // $("#phone").mask("(999) 999-9999");
    
    var form = $("form#user-auth-form");

    form.on('beforeSubmit', function () {
        var yiiform = $(this);
         $(".ajaxwait, .ajaxwait_image").show();
        // отправляем данные на сервер
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: yiiform.serializeArray(),
            }
        ).done(function (data) {
            $(".ajaxwait, .ajaxwait_image").hide();
            if (data.success) {
                window.location.href = '$successUrl';
            } else {
                $('#modal-user').modal('show')
                    .find('#modal-content')
                    .html(data.error ? data.error.err_text : 'Пожалуйста повторите ввод.');
            }
        }).fail(function (error) {
            $(".ajaxwait, .ajaxwait_image").hide();
            $('#modal-user').modal('show')
                .find('#modal-content')
                .html('Не удалось найти указанное направление');
        });
        
        return false;
    });
    //Сброс всех значений в SessionStorage при нажатии на отмену 
    $('#cancel-appt').on('click', function () {
        sessionStorage.clear();
        localStorage.clear();
    });
});
JS;
$this->registerJs( $js );