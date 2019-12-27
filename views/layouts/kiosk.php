<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\KioskAsset;
use yii\helpers\Html;
use yii\helpers\Url;

KioskAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <div class="ajaxwait"></div>
    <div class="ajaxwait_image">
        <?= Html::img('@web/images/autoLoader/ajaxloader2.gif') ?>
    </div>


    <header>
        <?= $this->render('header') ?>
    </header>

    <main>
        <div class="content">
            <?= $content ?>
        </div>
    </main>

    <?= $this->render('footer') ?>

<?php $this->endBody() ?>

<script>
    $(document).ready(function () {
        $(".ajaxwait, .ajaxwait_image").hide();
        $(".ajaxwait, .ajaxwait_image").ajaxSend(function (event, xhr, options) {
            $(this).show();
        }).ajaxStop(function () {
            $(this).hide();
        });
    });
</script>

</body>
</html>
<?php $this->endPage() ?>

<?php
var_dump($_SESSION);
//var_dump($GLOBALS);
?>
