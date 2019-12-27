<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 27.11.2018
 * Time: 12:39
 */
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="infoText text-center" style="opacity: 0; transition: opacity 0.8s">
    <?php if (isset($_SESSION['patientChoice']['sortCode'])) echo '<h2 id="officeName">' . $_SESSION['patientChoice']['officeName'] . '</h2>'?>
    <?php if (isset($_SESSION['patientChoice']['specName'])) echo '<h2 id="windowName">Выбранный специалист: ' . mb_strtoupper($_SESSION['patientChoice']['specName']) . ' (' . $_SESSION['patientChoice']['docName'] . ')</h2>';?>
    <?php if (isset($windowName)) echo '<h2 id="windowName">' . $windowName . '</h2>';?>
    <?php $kol = 36;?> <!--Количесвто номерков на странице без свайпера, со свайперои их на 6 штук меньше-->
</div>

<div id="inline" class="ui-widgett"></div>

<div class="days-step step">
    <div class="container-fluid">
        <h4 class="text-center"></h4>
        <?php foreach ($days as $key => $item): ?>
            <?= Html::beginTag('div', ['class' => 'dates-container', 'style' => 'display: none;', 'name' => 'd' . str_replace('.','',$key)]);?>
                <div class="row">
                    <?php if (count($item['rnumbs']) > $kol):?>
                    <?php $kolRnumb = $kol - 6;?>
                        <div class="col-sm-10">
                    <?php else:?>
                        <?php $kolRnumb = $kol;?>
                        <div class="col-sm-12">
                    <?php endif;?>
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php for ($i = 0; $i < count($item['rnumbs']); $i += $kolRnumb):?>
                                    <?php if ($i > count($item['rnumbs'])) {
                                        $limit = $kolRnumb + $i;
                                    } else {
                                        $limit = count($item['rnumbs']);
                                    }?>
                                        <div class="swiper-slide">
                                            <?php for ($j = $i; $j < $limit; $j++):?>
                                                <?= Html::a($item['rnumbs'][$j]['DAT_BGN'],
                                                Url::to([$url,
                                                    'rnumbId' => $item['rnumbs'][$j]['RNUMB_ID'],
                                                    'rnumbInfo' => $item['rnumbs'][$j]['DD'] . ' (' . $item['rnumbs'][$j]['DW'] . '), ' . $item['rnumbs'][$j]['DAT_BGN'],]),
                                                    ['class' => 'btn btn-rnumb', 'id' => $item['rnumbs'][$j]['RNUMB_ID']]) ?>
                                            <?php endfor; ?>
                                        </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <?php if (count($item['rnumbs']) > $kol): ?>
                        <div class="col-sm-2 pagination-container">
                            <button class="btn btn-default btn-navigation button-prev">
                                <i class="fas fa-chevron-circle-up"></i>
                            </button>
                            <button class="btn btn-default btn-navigation button-next">
                                <i class="fas fa-chevron-circle-down"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?= Html::endTag('div')?>
        <?php endforeach; ?>
    </div>
</div>
<?php
$homeUrl = Url::to(['site/index']);
$js2 = <<<JS

$(document).ready(function () {
    
    $('.infoText').css({opacity: '1'});

    currentDate = '$firstDate';
    $('h4.text-center').addClass('rnumb-title').html('Доступные номерки на ' + currentDate + 'г.');     
    currentTimesInDate = $('div[name = ' + 'd' + currentDate.replace(/\./g, "") + ']');
    currentTimesInDate.each(function(key, value){
        $(this).fadeIn('100');
    });    
    $('[name = ' + 'd' + currentDate.replace(/\./g, "") + '] a').each(function(key, value) {
        $(this).css({opacity: 1});
    });
    
    $('#inline').datepicker({
        minDate: new Date('$minDate'),
        maxDate: new Date('$maxDate'),
        setDate: '$minDate',
        showOtherMonths: false,
        beforeShowDay: funEnDisDate,
        onSelect: function(currentDate) {
            
            //Скрываем все номерки
            $('.dates-container').each(function(key, value){
                $(this).css({'display': 'none'});
            });
            
            //Показываем номерки только выбранной даты
            $('h4.text-center.rnumb-title').html('Доступные номерки на ' + currentDate + 'г.');
            currentTimesInDate = $('div[name = ' + 'd' + currentDate.replace(/\./g, "") + ']');
            currentTimesInDate.each(function() {
                $(this).fadeIn('100');
            });
            // Запускаем показ слайдов для всех слайдеров с самого первого при переключении между датами 
            for(var i = 0; i < mySwiper.length; i++) {
                mySwiper[i].slideTo (0, 0);
            }
        }
    });
       
    var mySwiper = new Swiper ('.swiper-container', {
        direction: 'vertical',
        loop: false,
        height: 506,
        slidesPerView: 1,
        navigation: {
            nextEl: '.button-next',
            prevEl: '.button-prev',
        },
    });
    
    $('[class="btn btn-rnumb"]').each(function(key, item){
        item.addEventListener('click', function(e) {
            e.preventDefault();
            sessionStorage.setItem('rnumbId', e.path[0].id);
            $.ajax({
                type: 'POST',
                data: {rnumbId: e.path[0].id},
                url: window.location.href,
            }).done(function(data) {
                if (data.success) {
                    console.log('Номерок успешно заблокировался');
                    window.location.href = item.href;
                }
                if (!data.success) {
                    console.log('Страница перезагрузилась, так как номерок был уже занят');
                    window.location.reload();
                }
            }).fail(function() {
                    sessionStorage.clear();
                    localStorage.clear();
                    window.location.href = '$homeUrl';
                    console.log('Ajax запрос к серверу не прошел. Приложение полностью сбросило все параметры SessionStorage и вернулось на главную страницу');
            });
        });
    });
});
   
    var arrMyDates = new Array();
    $('.dates-container').each(function() {
        let myDay = $(this).attr('name').slice(1, 3);
        let myMonth = $(this).attr('name').slice(3, 5);
        let myYear = $(this).attr('name').slice(5, 10);
        let myCurrentDate = myYear + '-' + myMonth + '-' + myDay;
        arrMyDates.push(myCurrentDate);
    });
    function funEnDisDate(d) {
        let dat = $.datepicker.formatDate('yy-mm-dd', d);
            if ($.inArray(dat, arrMyDates) != -1 ) {
                return [true];
            } else {
                return [false];
            }
        return [true];
    }
JS;
$this->registerJs($js2);
?>