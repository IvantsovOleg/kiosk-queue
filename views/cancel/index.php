<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 03.06.2019
 * Time: 16:30
 */

use yii\helpers\Html;
use yii\helpers\Url;

?>
    <div class="infoText text-center">
        <?php if (isset($_SESSION['patient'])): ?>
            <h3 id="officeName">Уважаемый(ая) <?php echo $_SESSION['patient']['fullName'] ?>!</h3>
        <?php endif; ?>
    </div>

    <?php if (isset($rnumbs) && count($rnumbs) > 0): ?>
        <div id="windowName" class="infoText" style="padding-top: 1.5vh;">Ваши предстоящие визиты</div>
        <hr class="primary infoText" style="padding-top: 1.5vh;">
    <div class="container-fluid" style="font-size: 35px;">
            <div class="row swiper-slide">
                <div class="col-md-2 col-md-offset-1 text-center cancel-list-main">Дата и время</div>
                <div class="col-md-6 text-center cancel-list-main">Доктор</div>
                <div class="col-md-2 text-center cancel-list-main">Отмена</div>
            </div>
            <div class="swiper-container" style="height: 46.5vh;">
                <div class="swiper-wrapper">
                    <?php foreach ($rnumbs as $rnumb): ?>
                        <div class="row swiper-slide">
                            <div class="col-md-2 col-md-offset-1 text-center cancel-list"><?= date_create($rnumb['DAT'])->Format('d.m.Y H:i') ?></div>
                            <div class="col-md-6 text-center cancel-list"><?= $rnumb['DOC'] ?></div>
                            <div class="col-md-2 text-center cancel-btn btn-rnumb-cancel" data-rnumb-id="<?php echo $rnumb['NUMBID']?>">Отменить</div>
                        </div>
                    <?php endforeach; ?>
                </div>
                    <?php if (count($rnumbs)>6):?>
                        <div style="position: absolute; right: 6vw; top: 0; width: 2.8vw; z-index: 1000;">
                        <button class="btn btn-default btn-navigation button-prev" style="height: 22.3vh;">
                            <i class="fas fa-chevron-circle-up"></i>
                        </button>
                        <button class="btn btn-default btn-navigation button-next" style="height: 22.3vh">
                            <i class="fas fa-chevron-circle-down"></i>
                        </button>
                        </div>
                    <?php endif;?>
            </div>
<!--            <table class="swiper-container" style="display: inline-block; width: 100vw; font-size: 25px;">-->
<!--                <thead>-->
<!--                    <tr>-->
<!--                        <th>Дата и время приема</th>-->
<!--                        <th>Доктор</th>-->
<!--                        <th>Отмена</th>-->
<!--                    </tr>-->
<!--                </thead>-->
<!--                <tbody class="swiper-wrapper">-->
<!--                            --><?php //foreach ($rnumbs as $rnumb): ?>
<!--                                    <tr class="swiper-slide" style="display: auto;">-->
<!--                                        <td>--><?//= date_create($rnumb['DAT'])->Format('d.m.Y H:i') ?><!--</td>-->
<!--                                        <td>--><?//= $rnumb['DOC'] ?><!--</td>-->
<!--                                        <td>--><?//= Html::button('Отменить', ['class' => 'btn btn-danger btn-rnumb-cancel',
//                                                'data-rnumb-id' => $rnumb['NUMBID']]) ?>
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                            --><?php //endforeach; ?>
<!--                </tbody>-->
<!--            </table>-->

        <? else: ?>
            <h1 class="text-center infoText">Предстоящих визитов не найдено!</h1>
            <hr class="primary infoText" style="padding-top: 1.5vh;">
        <?php endif; ?>
    </div>


<?php
$footer = '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>';

yii\bootstrap\Modal::begin([
    'header' => 'Ошибка отмены визита',
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
$cancelUrl = Url::to(['cancel/delete']);
$js = <<<JS
$(document).ready(function () {
    
    $('.infoText').css({opacity: '1'});
    
    let mySwiper = new Swiper ('.swiper-container', {
        direction: 'vertical',
        loop: false,
        // height: 480,
        // autoHeight: true,
        // setWrapperSize: true,
        slidesPerView: 6,
        // spaceBetween: 3,
        slidesPerGroup: 6,
        speed: 800,
        navigation: {
            nextEl: '.button-next',
            prevEl: '.button-prev',
        },
    });
    
    $('.btn-rnumb-cancel').on('click', function() {
        const tr = $(this).parent();
        const numbId = $(this).attr('data-rnumb-id');
        $.ajax({
            url: '$cancelUrl',
            data: {numbid: numbId},
            type: "POST"
        }).done(function(data) {
            if (data.success) {
                tr.fadeOut(500, function() {
                    tr.remove();
                    if ($('.btn-rnumb-cancel').length == 0) {
                        window.location.reload();   
                    }
                });
            } else {
                $('#modal-user').modal('show')
                    .find('#modal-content')
                    .html(data.error ? data.error : 'Не удалось отменить Ваш визит пожалуйста обратитесь в регистратуру');
            }
        }).fail(function(data) {
            $('#modal-user').modal('show')
                    .find('#modal-content')
                    .html(data.error ? data.error : 'Не удалось отменить Ваш визит пожалуйста обратитесь в регистратуру');
        });
    });
});
JS;
$this->registerJs($js);


