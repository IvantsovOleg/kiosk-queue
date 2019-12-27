<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 27.11.2018
 * Time: 13:02
 */

use yii\helpers\Html;
use yii\helpers\Url;

?>
    <div class="confirm-step text-center">
        <div class="container-fluid">
            <?php if (isset($_SESSION['patient'])): ?>
                <h3>Уважаемый(ая) <strong><?php echo $_SESSION['patient']['fullName'] ?></strong>!</h3>
                <hr class="primary">
            <?php endif; ?>

            <div class="confirm">Пожалуйста, подтвердите запись на прием</div>

            <?php if (isset($_SESSION['patientChoice']['officeName'])): ?>
                <div class="confirm-info">
                    <div class="row">
                        <div class="col-sm-4 text-right">Запись в:</div>
                        <div class="col-sm-8 text-left"><strong><?php echo $_SESSION['patientChoice']['officeName'] ?></strong></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['patientChoice']['specName'])): ?>
                <div class="confirm-info">
                    <div class="row">
                        <div class="col-sm-4 text-right">Специальность:</div>
                        <div class="col-sm-8 text-left"><strong><?php echo $_SESSION['patientChoice']['specName'] ?></strong></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['patientChoice']['docName'])): ?>
                <div class="confirm-info">
                    <div class="row">
                        <div class="col-sm-4 text-right">Врач:</div>
                        <div class="col-sm-8 text-left"><strong><?php echo $_SESSION['patientChoice']['docName'] ?></strong></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['patientChoice']['rnumbInfo'])): ?>
                <div class="confirm-info">
                    <div class="row">
                        <div class="col-sm-4 text-right">Дата и время приема:</div>
                        <div class="col-sm-8 text-left"><strong><?php echo $_SESSION['patientChoice']['rnumbInfo']?></strong></div>
                    </div>
                </div>
            <?php endif; ?>

<!--            <hr class="primary" style="margin-bottom: 5vh">-->

            <div class="confirm-control" style="margin-top: 5vh">
                <?= Html::a('Отменить запись', '#', ['id' => 'cancel-appt', 'class' => 'btn btn-danger btn-rnumb-cancel btn-kiosk-sm']) ?>
                <?= Html::a('Подтвердить запись', '#', ['id' => 'confirm-appt', 'class' => 'btn btn-success btn-rnumb-success btn-kiosk-sm']) ?>
            </div>
        </div>
    </div>
    <button style="display: none;" class="btnPrint">Печать талона</button>

<?php yii\bootstrap\Modal::begin([
    'header' => 'Запись на прием',
    'id' => 'modal-confirm',
    'size' => 'modal-lg',
]);?>
    <div id='modal-content'>
        <h3 class="text-center">
            Загрузка...
        </h3>
    </div>
<?php yii\bootstrap\Modal::end(); ?>

<?php
$printUrl = Url::to(['site/print']);

$js = <<<JS
$(document).ready(function () {
    $('#confirm-appt').on('click', function (e) {
        e.preventDefault();
        $.ajax({
                type: 'POST',
                url: window.location.href,
                data: {action: 'confirm',},
        }).done(function () {
            $('.btnPrint').printPage({
                url: '$printUrl',
                attr: "href",
                showMessage: false,
                // message:"Печать талона ...",
                afterCallback: function() {
                    setTimeout(function() {
                        window.location.href = window.location.pathname;
                    }, 9000); 
                }
            });
            $('.btnPrint').click();
            $('.confirm-control').html('<div class=\'messageAfterPrintTalon\'>' +
                                            '<div><strong>Запись успешно произведена! Ваш талон печатается.</strong></div>' +
                                            '<span>Переадресация на главную страницу через&nbsp</span>' +
                                            '<span id="timer_inp">10</span>' +
                                            '<span>&nbspсек.</span>' +
                                        '</div>');
                                        function backTimer(step){
                                            let obj = document.querySelector('#timer_inp');
                                            obj.innerHTML--;
                                            if(obj.innerHTML == 0){
                                                setTimeout(function(){}, step);}
                                            else {
                                                setTimeout(backTimer, step, step);
                                            }
                                        }
                                        setTimeout(backTimer, 1000, 1000);
        }).fail(function () {
            $('#modal-confirm').modal('show').find('#modal-content').html('Возникла ошибка пожалуйста повторите запись');
            window.location.href = window.location.pathname;
        });
        return false;
    });
    
    $('#cancel-appt').on('click', function (e) {
        e.preventDefault();
        $.ajax({
                type: 'POST',
                url: window.location.href,
                data: {action: 'cancel',},
        }).done(function(data) {
            if (data.success) {
                console.log('Блокировка с номерка была снята');
            } else if (!data.success) {
                console.log('Блокировка c номерка не снята');
            }
           window.location.href = window.location.pathname;
        }).fail(function() {
            console.log('Ajax запрос на снятие блокировки с номерка не прошел');
            window.location.href = window.location.pathname;
        });
        sessionStorage.clear();
        localStorage.clear();
    });
});
JS;
$this->registerJs($js);
