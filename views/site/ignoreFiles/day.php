<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 27.11.2018
 * Time: 12:39
 */

use yii\helpers\Html;
use yii\helpers\Url;

//echo "<pre>";
//print_r($days);
//echo "</pre>";
?>

<div class="info">
    <?php if (isset($specname)): ?>
        <div>Выбранная специальность: <?= $specname ?></div>
    <?php endif; ?>
    <?php if (isset($docname)): ?>
        <div>Врач: <?= $docname ?></div>
    <?php endif; ?>
</div>

<div class="days-step step">

    <div class="title">
        Пожалуйста, выберите время
    </div>

    <div class="container-fluid">
        <?php if (isset($days) && count($days) > 0): ?>
        <div class="row">

            <?php if (count($days) > 5): ?>
            <div class="col-sm-10 col-sm-offset-1">
                <?php else: ?>
                <div class="col-sm-10 col-sm-offset-1">
                    <?php endif; ?>
                    <div class="swiper-container swiper-box-one" style="height: 600px;">
                        <div class="swiper-wrapper">
                            <?php foreach ($days as $item): ?>
                                <div class="swiper-slide">

                                    <h4 class="text-center rnumb-title"><?= $item['dd'] ?> (<?= $item['dw'] ?>)</h4>

                                    <?php if (count($item['rnumbs']) > 0): ?>

                                        <div class="rnumbs-container">
                                            <?php foreach ($item['rnumbs'] as $rnumb): ?>

                                                <?= Html::a($rnumb['dat_bgn'],
                                                    Url::to(['record/auth',
                                                        'rnumbid' => $rnumb['rnumb_id'],
                                                        'rnumbinfo' => $rnumb['dd'] . ' (' . $rnumb['dw'] . '), ' . $rnumb['dat_bgn']
                                                    ]),
                                                    ['class' => 'btn btn-rnumb']) ?>

                                            <?php endforeach; ?>
                                        </div>

                                    <?php endif; ?>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php if (count($days) > 5): ?>
                    <div class="col-sm-1 pagination-container">
                        <button class="btn btn-default btn-pagination button-prev"><i
                                    class="fas fa-chevron-circle-up"></i></button>
                        <button class="btn btn-default btn-pagination button-next"><i
                                    class="fas fa-chevron-circle-down"></i></button>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
