<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="infoText text-center" style="opacity: 0; transition: opacity 0.8s">
    <?php if (isset($windowName)): ?>
        <h2 id="windowName"><?php echo $windowName; ?></h2>
        <hr class="primary">
    <?php endif; ?>
</div>
<div class="site-index">
    <div class="col-sm-10 col-sm-offset-1">
		<?php if ( isset( $btns ) && count( $btns ) > 0 ): ?>
            <div class="row">
				<?php foreach ( $btns as $item ): ?>
                    <div class="col-sm-12">
						<?= Html::a( $item['title'], Url::to( [
							$item['url'],
							'sortCode' => $item['sortCode']
						] ), [ 'class' => 'btn btn-block btn-primary btn-kiosk' ] ) ?>
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