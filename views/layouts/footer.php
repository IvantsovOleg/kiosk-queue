<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2">
                <?php if (Yii::$app->session->get('isHome') == 1): ?>
                    <div class="bottom-control" id="back" style="font-size: 2em;text-align: center">Вернуться</div>
                <?php endif; ?>
            </div>
            <div class="col-sm-8">
                <div class="title">
                    <!--Для разноса кнопок "Вернуться" и "На главную" по разным углам экрана-->
                </div>
            </div>
            <div class="col-sm-2 text-right">
                <?php if (Yii::$app->session->get('isHome') == 1): ?>
                    <div class="bottom-control" id="home" style="font-size: 2em;text-align: center">На главную</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>