<div class="info auth-info">
    <?php if (isset($specName)): ?>
        <div id="windowName" style="padding-bottom: 0;">Выбранная специальность: <strong><?= mb_strtoupper($specName) ?></strong></div>
    <?php endif; ?>
    <?php if (isset($docName)): ?>
        <div id="windowName" style="padding-top: 0; padding-bottom: 0;">Врач: <strong><?= mb_strtoupper($docName) ?></strong></div>
    <?php endif; ?>
</div>

<div class="fullcalendar-widget">
    <?= yii2fullcalendar\yii2fullcalendar::widget([
        'options' => [
           'lang' => 'ru',
            //... more options to be defined here!
        ],
        'clientOptions' => [
            'height' => 710,
        ],
        'events' => $events,
    ]);
    ?>
</div>




