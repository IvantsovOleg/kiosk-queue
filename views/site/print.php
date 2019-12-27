<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 18.03.2019
 * Time: 14:04
 */

use yii\helpers\Html;

if (isset($rnumb)): ?>
    <div class="print-talon">
        <?php if (isset($_SESSION['patient']['shortName'])): ?>
            <div>Пациент:</div>
            <div class="print-talon__speciality"><?= $_SESSION['patient']['shortName'] ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['patientChoice']['rnumbId'])): ?>
            <div>Номер талона:</div>
            <div class="print-talon__talonNumber"><?= $_SESSION['patientChoice']['rnumbId'] ?></div>
        <?php endif; ?>

        <?php if (isset($rnumb['SPEC'])): ?>
            <div>Специальность:</div>
            <div class="print-talon__doctor"><?= $rnumb['SPEC'] ?></div>
        <?php endif; ?>

        <?php if (isset($rnumb['LASTNAME'])): ?>
            <div>Врач:</div>
            <div class="print-talon__doctor"><?= $rnumb['LASTNAME'] ?> <?= $rnumb['FIRSTNAME'] ?> <?= $rnumb['SECONDNAME'] ?></div>
        <?php endif; ?>

        <?php if (isset($rnumb['DAT_BGN'])): ?>
            <div>Время приема:</div>
            <div class="print-talon__date"><?= date_create($rnumb['DAT_BGN'])->Format('d.m.Y H:i') ?></div>
        <?php endif; ?>

        <?php if (isset($rnumb['CAB'])): ?>
            <div>Кабинет:</div>
            <div class="print-talon__cabinet"><?= $rnumb['CAB'] ?></div>
        <?php endif; ?>

        <?php if (isset($rnumb['ADDR'])): ?>
            <div>Адрес места приема:</div>
            <div class="print-talon__doctor"><?= $rnumb['ADDR'] ?></div>
        <?php endif; ?>

        <?php if (isset($rnumb['RNUMB_ID'])): ?>
            <div class="print-talon__barcode"><?= $barGenerator->getBarcode($rnumb['RNUMB_ID'], $barGenerator::TYPE_CODE_128); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>



