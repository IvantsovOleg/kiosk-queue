<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="ajaxwait"></div>
<div class="ajaxwait_image">
    <?= Html::img('@web/images/ajaxloader.gif') ?>
</div>


<div class="wrap">

    <?= $this->render('_header') ?>

    <div class="container content">
        <?= Breadcrumbs::widget([
            'homeLink' => ['label' => 'Главная', 'url' => Url::to(['site/index'])],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?= $this->render('_footer') ?>

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
