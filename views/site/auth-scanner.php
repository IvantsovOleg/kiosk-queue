<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 09.04.2019
 * Time: 9:57
 */

use yii\helpers\Url;

?>
    <div class="auth-scanner">
        <?php if (isset ($_SESSION['patientChoice'])): ?>
            <?php echo '<h2 id="officeName">' . Yii::$app->session['patientChoice']['officeName'] . '</h2>'; ?>
            <div style="margin-bottom: 10vh; font-size: 3.5vh" class="text-center" id="patientChoice">
                <?php echo 'Ваш выбор: ' . mb_strtoupper($_SESSION['patientChoice']['specName']) . ' ' . $_SESSION['patientChoice']['docName'] . '<br>' . 'Время приема: ' . $_SESSION['patientChoice']['rnumbInfo'] ?>
            </div>
        <?php endif; ?>
        <div class="auth_scanner__title">
            Пожалуйста, поднесите бумажный полис ОМС к считывателю<br>или вставьте пластиковый полис ОМС в считыватель
        </div>

        <hr class="primary">

        <div class="auth_scanner__status">
            <span class="message"></span>
        </div>

    </div>
<?php
$scannerStop = Url::to(['/reader/stop']);
$scannerInfo = Url::to(['/reader/info']);
$scannerStatus = Url::to(['/reader/status']);
$scannerStart = Url::to(['/reader/start']);
$scannerPolicy = Url::to(['/reader/get-data']);
$setSessionPolicy = Url::to(['/reader/set-policy']);

if (Yii::$app->session['action'] == 'cancel') {
    $returnUrl = Url::to(['cancel/type-auth']);
    $successUrl = Url::to(['cancel/index']);
} else {
    $returnUrl = Url::to(['site/choose-type-auth', 'rnumbId' => $_SESSION['patientChoice']['rnumbId'], 'rnumbInfo' => $_SESSION['patientChoice']['rnumbInfo'], 'sortCode' => $_SESSION['patientChoice']['sortCode'], 'specId' => $_SESSION['patientChoice']['specId'], 'specName' => $_SESSION['patientChoice']['specName'], 'docId' => $_SESSION['patientChoice']['docId'], 'docName' => $_SESSION['patientChoice']['docName'],]);
    $successUrl = Url::to(['record/success']);
}

$js = <<<JS
$(document).ready(function () {
    
    const successTime = 3000;
    const errorTime = 2000;
    const message = document.querySelector('.auth_scanner__status > span.message');
    message.innerHTML = 'Ожидание полиса';

    function startScan() {
        axios.get('$scannerStop')
            .then(() => {
                return axios.get('$scannerInfo');
            }).then(info => {
                if (info && info.data) {
                    return axios.get('$scannerStatus');
                }
            }).then(status => {
                if (status && status.data && status.data.status === 'OK') {
                    return axios.get('$scannerStart')
                } else {
                    message.innerHTML = 'Сканер занят, попытка повторного запуска';
                    setTimeout(() => {
                        startScan();
                    }, 1000);
                }
            }).then(start => {
                if (start && start.data.status === 'OK') {
                    getScannerData();
                }
            }).catch(error => {
                console.log(error);
            });
    }

    function getScannerData() {
      axios.get('$scannerStatus').then(status => {
        if (status && status.data && status.data.status === 'OK') {
          message.innerHTML = 'Считывание полиса';  
          axios.get('$scannerPolicy').then(policy => {
              if (policy && policy.data) {
                const policyData = policy.data;
                axios.post('$setSessionPolicy', policyData).then(response => {
                  if (response && response.data && response.data.success) {
                    message.innerHTML = 'Пациент найден, Вы будете перенаравлены на страницу подтверждения';
                    setTimeout(() => {
                      window.location.href = '$successUrl';
                    }, errorTime);
                  } else {
                    message.innerHTML = (response && response.data && response.data.user_error) ? response.data.user_error : 'Пациент не найден';
                    setTimeout(() => {
                      window.location.href = '$returnUrl';
                    }, successTime);
                  }
                })
              } else {
                message.innerHTML = 'Полис не найден';
                setTimeout(() => {
                    window.location.href = '$returnUrl';
                }, errorTime);
              }
          })
        } else {
          getScannerData();
        }
    });
    }
    startTime();
    startScan();
});
JS;
$this->registerJs($js);