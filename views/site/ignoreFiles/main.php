<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="site-index">
    <h2 id="officeName"><?php echo $_SESSION['patientChoice']['officeName']; ?></h2>
    <h3 id="windowName">Вы авторизованы как <?= $patient['fullName'] ?></h3>
    <h2 id="patientChoice"
        style="margin-bottom: 0px"><?php echo 'Ваш выбор: ' . mb_strtoupper($_SESSION['patientChoice']['specName']) . ' '
            . $_SESSION['patientChoice']['docName'] . '<br>' . 'Время приема: ' . $_SESSION['patientChoice']['rnumbInfo'] ?></h2>
    <hr class="primary">
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
