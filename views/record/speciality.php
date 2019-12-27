<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 27.11.2018
 * Time: 12:39
 */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="speciality">
    <?php if (isset($_SESSION['patientChoice']['sortCode'])):?>
        <div class="infoText text-center" style="opacity: 0; transition: opacity 0.8s">
            <h2 id="officeName"><?php echo $_SESSION['patientChoice']['officeName'];?></h2>
        <?php if (isset($windowName)): ?>
            <h2 id="windowName"><?php echo $windowName; ?></h2>
        <?php endif;?>
        <hr class="primary" style="padding-bottom: 20px">
        </div>;
    <?php endif;?>
    <div class="container-fluid">
        <?php if (isset($speciality) && count($speciality) > 0): ?>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="swiper-container swiper-box" style="height: 600px;">
                    <div class="swiper-wrapper">
                        <?php foreach ($speciality as $item): ?>
                            <div class="swiper-slide">
                                <?= Html::a($item['SPEC_NAME'] . ' ' . $item['TEXT_COUNT'],
                                    Url::to([$url, 'specId' => $item['SPEC_ID'], 'specName'=>$item['SPEC_NAME']]), ['class' => 'btn btn-block btn-primary btn-kiosk-sm']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php if (count($speciality) > 5): ?>
                <div class="col-sm-1 pagination-container">
                    <button class="btn btn-default btn-pagination button-prev">
                        <i class="fas fa-chevron-circle-up"></i>
                    </button>
                    <button class="btn btn-default btn-pagination button-next">
                        <i class="fas fa-chevron-circle-down"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
$js = <<<JS
$(document).ready(function() {
  $('.infoText').css({opacity: '1'});
});
JS;
$this->registerJs($js);
?>