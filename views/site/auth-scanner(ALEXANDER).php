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
        <div class="auth_scanner__title">
            Пожалуйста поднесите бумажный полис ОМС к считывателю или вставьте пластиковый полис ОМС в считыватель.
        </div>

        <hr class="primary">

        <div class="auth_scanner__status">
            <span class="message">Ожидание полиса</span>
        </div>

    </div>

<?php
$scannerInfo = Url::to(['/reader/info']);
$scannerStart = Url::to(['/reader/start']);
$scannerStop = Url::to(['/reader/stop']);
$scannerStatus = Url::to(['/reader/status']);
$scannerPolicy = Url::to(['/reader/get-data']);
$setSessionPolicy = Url::to(['/reader/set-policy']);

$returnUrl = Url::to(['/kiosk/main/select-auth']);
$successUrl = Url::to(['/kiosk/main/main']);

$js = <<<JS
$(document).ready(function () {

  const pollingTime = 400;
  const successTime = 3000;
  const errorTime = 2000;
  const message = document.querySelector('.auth_scanner__status > span.message');
  message.innerHTML = 'Ожидание полиса';

  function startScan() {
    axios.get('$scannerStop')
      .then(() => {
        return axios.get('$scannerInfo');
      })
      .then(info => {
        if (info && info.data) {
          return axios.get('$scannerStatus');
        }
      })
      .then(status => {
        if (status && status.data && status.data.status === 'OK') {
          return axios.get('$scannerStart')
        } else {
          message.innerHTML = 'Сканер занят, попытка повторного запуска';
          startScan();
        }
      })
      .then(start => {
        if (start && start.data.status === 'OK') {
          getScannerData();
        }
      })
      .catch(error => {
        console.log(error);
      });
  }

  function getScannerData() {
    const time = setInterval(() => {
      axios.get('$scannerStatus')
        .then(status => {
          if (status && status.data && status.data.status === 'OK') {
            axios.get('$scannerPolicy')
              .then(policy => {
                message.innerHTML = 'Считывание полиса';
                if (policy && policy.data) {
                  const policyData = policy.data;
                  clearInterval(time);
                  axios.post('$setSessionPolicy', policyData)
                    .then(response => {
                      if (response && response.data && response.data.success) {
                        message.innerHTML = 'Пациент найден, Вы будете перенаравлены на страницу подтверждения';
                        clearInterval(time);
                        setTimeout(() => {
                          window.location.href = '$successUrl';
                        }, errorTime);
                      } else {
                        message.innerHTML = (response && response.data && response.data.user_error) ? response.data.user_error : 'Пациент не найден';
                        clearInterval(time);
                        setTimeout(() => {
                          window.location.href = '$returnUrl';
                        }, successTime);
                      }
                    })
                } else {
                  message.innerHTML = 'Полис не найден';
                  clearInterval(time);
                  setTimeout(() => {
                    window.location.href = '$returnUrl';
                  }, errorTime);
                }
              })
          }
        })
    }, pollingTime);
  }
  // startTime();
  startScan();
});
JS;
$this->registerJs($js);