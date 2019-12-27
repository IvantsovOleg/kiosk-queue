<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div style="text-align: center">
    <?= Html::a(Html::img('@web/images/errors/error-image.svg', ['class' => 'error-404-img']),Url::to(['site/index']))?>
</div>
<div style="text-align: center">
    <h1>ERROR 404</h1>
    <h1>Ой-ой! Страницы не существует... нужно делать?</h1>
</div>
