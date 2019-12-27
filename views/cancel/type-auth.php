<?php

/* @var $this yii\web\View */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \yii\helpers\Url;

?>
<div class="infoText text-center" style="opacity: 0; transition: opacity 0.8s">
    <h2 id="officeName">Уважаемый пациент!</h2>
    <?php echo '<h2 id="windowName">' . $windowName . '</h2>';?>
    <hr class="primary">
</div>
<div class="site-index">
    <div class="col-sm-10 col-sm-offset-1">
        <?php if (isset($btns) && count($btns) > 0): ?>
            <div class="row">
                <?php foreach ($btns as $item): ?>
                    <div class="col-sm-12">
                        <?= Html::a($item['title'],
                            Url::to([$item['url']]), ['class' => 'btn btn-block btn-primary btn-kiosk']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
  let siteIndex = $('.site-index').height();
  let content = $('.content').height();
  let infoText = $('.infoText').height();
  let topHeight = ((content/2 - siteIndex/2) - infoText) - 20;
  $('.infoText').css({position: 'relative', top: topHeight, opacity: '1'});
});
JS;
$this->registerJs($js);
?>