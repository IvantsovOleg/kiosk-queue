<?php

use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-sm-1 text-left">
                <?= Html::img('@web/images/logo/logo.png', [
                    'class' => 'img-responsive',
                    'style' => 'margin-top: 1.2vh; max-height: 8.4vh; max-width: 8.4vh;'
                ]) ?>
            </div>
            <div class="col-sm-10 text-center">
                <?= Html::tag('div', 'Государственное бюджетное учреждение здравоохранения', ['style' => 'color: #00a163; margin-top: 1.5vh; font-size: 1.23vw;']) ?>
                <?= Html::tag('div', '«Городская поликлиника №44»',['style' => 'font-size: 2.2vw; font-weight: 700; color: #00a163;']) ?>
            </div>
            <div class="col-sm-1 text-center">
                <div class="date-time">
                    <div id="date"></div>
                    <div id="time"></div>
                </div>
            </div>
        </div>
    </div>
</div>

