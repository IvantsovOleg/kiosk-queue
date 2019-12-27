<?php

/* @var $this yii\web\View */

use app\assets\KioskAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Выберете учреждение';
//$reg_code = $_GET[reg_code];
//print_r( $reg_code);
//$ip = Yii::$app->request->userIP;
//print_r( $ip);

?>
<div class="site-index">
    <div>
        <h2 style="text-align: center; margin-bottom: 1.5%; font-weight: 700; color: #00a163;">ВЫБЕРЕТЕ УЧРЕЖДЕНИЕ</h2>
        <hr class="primary">
    </div>

    <div class="col-sm-10 col-sm-offset-1">
        <?php if (isset($btns) && count($btns) > 0): ?>
            <div class="row">
                <?php foreach ($btns as $item): ?>
                    <div class="col-sm-12">
                        <?= Html::a($item['title'], Url::to([$item['url']]), ['class' => 'btn btn-block btn-primary btn-kiosk']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
