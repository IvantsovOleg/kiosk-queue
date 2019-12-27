<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <div class="container-fluid" style="border-bottom: 0.3em solid #ddd;">
        <div class="row">
            <div class="col-sm-8">
                <div class="auth-form">

                    <h3 class="title text-center">
                        Пожалуйста, авторизуйтесь
                    </h3>

                    <?php $form = ActiveForm::begin([
                        'id' => 'user-auth-form',
                        'options' => ['class' => 'form-horizontal'],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-sm-8\">{input}</div>",
                            'labelOptions' => ['class' => 'col-sm-4 control-label'],
                        ],
                    ]) ?>

                    <?= $form->field($model, 'lastname')->label('Фамилия:*') ?>
                    <?= $form->field($model, 'firstname')->label('Имя:*') ?>
                    <?= $form->field($model, 'secondname')->label('Отчество:*') ?>
                    <?= $form->field($model, 'dob')->label('Дата рождения:*')->textInput(['id' => 'dob']) ?>


                    <?php ActiveForm::end() ?>

                </div>
            </div>
            <div class="col-sm-4">
                <div class="info auth-info">
                    <p style="font-weight: 700;">Информация о записи:</p>
                    <?php if (isset($spec)): ?>
                        <div>Выбранная специальность: <?= $spec['specName'] ?></div>
                    <?php endif; ?>
                    <?php if (isset($doc)): ?>
                        <div>Врач: <?= $doc['docName'] ?></div>
                    <?php endif; ?>
                    <?php if (isset($rnumb)): ?>
                        <div>Талон: <?= $rnumb['info'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
<div class="keyboard-container">
        <?= $this->render('_keyboard'); ?>
    </div>
    

<?php
$footer = Html::a('Отмена записи', Url::to(['site/index']), ['class' => 'btn btn-danger']);
$footer .= '<button type="button" class="btn btn-default" data-dismiss="modal">Повторить ввод</button>';

yii\bootstrap\Modal::begin([
    'header' => 'Ошибка авторизации',
    'footer' => $footer,
    'id' => 'modal-user',
    'size' => 'modal-lg',
]);
?>
    <div id='modal-content'>
        <h3 class="text-center">
            Загрузка...
        </h3>
    </div>


<?php yii\bootstrap\Modal::end(); ?>

<?php
$successUrl = Url::to(['record/success', 'rnumbid' => $rnumbid]);
$js = <<<JS
$(document).ready(function () {
    $("#dob").mask("99.99.9999");
  //  $("#phone").mask("(999) 999-9999");

    var form = $("form#user-auth-form");

    form.on('beforeSubmit', function () {
        var yiiform = $(this);
        // отправляем данные на сервер
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: yiiform.serializeArray()
            }
        ).done(function (data) {
            if (data.success) {
                window.location.href = '$successUrl' + '&patientid='+data.user;
            } else {
                $('#modal-user').modal('show')
                    .find('#modal-content')
                    .html(data.user_error ? data.user_error : 'Пожалуйста повторите ввод.');
            }
        }).fail(function () {
            $('#modal-user').modal('show')
                .find('#modal-content')
                .html('Не удалось выполнить проверить пользователя.');
        });
        return false;
    });
});
JS;
$this->registerJs($js);
