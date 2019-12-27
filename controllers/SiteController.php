<?php

namespace app\controllers;

use app\models\AuthNumber;
use app\models\Auth;
use Yii;
use yii\web\Response;
use Picqer\Barcode\BarcodeGeneratorSVG;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class SiteController extends AppController
{
    /**
     * ГЛАВНАЯ СТРАНИЦА. Выбор основного действия
     * @return array|string
     */
    public function actionIndex(){

        $session = Yii::$app->session;
        $session->removeAll();

        $this->view->title = 'Главная страница';
        $windowName = 'ВЫБЕРЕТЕ ПУНКТ МЕНЮ';

        $btns = [
            [
                'title' => 'ЗАПИСЬ НА ПРИЕМ',
                'url' => 'site/office'
            ],
            [
                'title' => 'РАСПИСАНИЕ',
                'url' => 'schedule/index'
            ],
            [
                'title' => 'ОТМЕНА ЗАПИСИ',
                'url' => 'cancel/type-auth',
            ],
        ];
        return $this->render('index', compact('btns', 'windowName'));
    }

    /**
     * ВЫБОР УЧРЕЖДЕНИЯ. Пользователь попадает сюда выбрав в меню "Запись на прием".
     * Исходя из специфики Городской поликлиники №44, она содержит в себе еще 4 других учреждения.
     * В связи с чем появилось дополнительное окно выборки. После выбора необходимой поликлиники на следующем этапе
     * происходит запись переменной markerForNextStep в сессию для дальнейшего использования ее для обращения к базе данных.
     * Доступ пользователя к информации из базы данных осуществляется без авторизации.
     * @return array|string
     */
    public function actionOffice() {

        $session = Yii::$app->session;
        unset($_SESSION['patientChoice']);

        $this->view->title = 'Выбор учреждения';
        $offices = $this->offices;
        $session->set('isHome', '1');
        $windowName = 'ВЫБЕРЕТЕКУДА ВЫ ХОТИТЕ ЗАПИСАТЬСЯ';

        $btns = [];
        foreach ($offices as $value) {
            array_push($btns,[
                'title' => $value['officeName'],
                'url' => 'site/type-of-record',
                'sortCode' => $value['sortCode'],
            ]);
        }

        return $this->render('index', compact('btns', 'windowName'));
    }

    /**
     * ВЫБОР ТИПА ЗАПИСИ. Пользователь попадает сюда выбрав в меню "Запись на прием" -> "Наименование учреждения".
     * На этом этапе происходит запись переменной markerForNextStep, которая приходит в составе HTML запроса
     * в сессию для дальнейшего использования ее для обращения к базе данных.
     * Доступ пользователя к информации из базы данных осуществляется без авторизации.
     * @param $markerForNextStep - Наименование выбранного учреждения
     * @return array|string
     */
    public function actionTypeOfRecord($sortCode) {

        $session = Yii::$app->session;
        $session->set('isHome', '1');
        $windowName = 'ВЫБЕРЕТЕ ТИП ЗАПИСИ';
        $this->view->title = 'Выбор типа записи';
        foreach ($this->offices as $item) {
            if ($sortCode == $item['sortCode']) {
                $officeName = $item['officeName'];
            }
        }
        $session['patientChoice'] = [
            'sortCode' => $sortCode,
            'officeName' => $officeName,
            ];

        // Задаем условие при котором "Запись на диспанесеризацию" не будет появляться
        if ($sortCode == $this->offices['office1']['sortCode'] || $sortCode == $this->offices['office4']['sortCode']) {
            $btns = [
                [
                    'title' => 'СВОБОДНАЯ ЗАПИСЬ К ВРАЧУ',
                    'url' => 'record/speciality',
                ],
                [
                    'title' => 'ЗАПИСЬ НА ДИСПАНСЕРИЗАЦИЮ',
                    'url' => 'site/not-exist',
                ],
                [
                    'title' => 'ЗАПИСЬ ПО НАПРАВЛЕНИЮ',
                    'url' => 'site/not-exist',
                ],
            ];
        } elseif ($sortCode == $this->offices['office2']['sortCode'] || $sortCode == $this->offices['office3']['sortCode']) {
            $btns = [
                [
                    'title' => 'СВОБОДНАЯ ЗАПИСЬ К ВРАЧУ',
                    'url' => 'record/speciality',
                ],
                [
                    'title' => 'ЗАПИСЬ ПО НАПРАВЛЕНИЮ',
                    'url' => 'site/not-exist',
                ],
            ];
        }
        return $this->render('index', compact('btns', 'windowName'));
    }

    /**
     * Выбор метода авторизации пациента перед записью с блокировкой выбранного номерка
     * @return array|string
     */
    public function actionChooseTypeAuth($rnumbId, $rnumbInfo)
    {
        $_SESSION['patientChoice']['rnumbId'] = $rnumbId;
        $_SESSION['patientChoice']['rnumbInfo'] = $rnumbInfo;
        $_SESSION['isHome'] = 1;

            $this->view->title = 'Выбор типа авторизации';
//            $session = Yii::$app->session;

            $windowName = 'ДЛЯ ПРОДОЛЖЕНИЯ НЕОБХОДИМО АВТОРИЗОВАТЬСЯ. ВЫБЕРЕТЕ ТИП АВТОРИЗАЦИИ';
//            $offices = $this->offices;

            //Если запрос Ajax, то это возврат к предыдущему окну, поэтому нужно снять блокировку с номерка, выбранному ранее
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $rnumbUnBlock = $this->unsetBlockRnumb(Yii::$app->request->get()['rnumbId']);
                if (isset($rnumbUnBlock) && ($rnumbUnBlock[0]['ERR_CODE']==0)){
                    return ['success' => true];
                } else {
                    return ['success' => false];
                }
            }

            $btns = [
                [
                    'title' => 'АВТОРИЗАЦИЯ ПО ФИО',
                    'url' => 'site/auth',
                ],
                [
                    'title' => 'АВТОРИЗАЦИЯ ПО НОМЕРУ ПОЛИСА ОМС',
                    'url' => 'site/auth-number',
                ],
                [
                    'title' => 'АВТОРИЗАЦИЯ ПО ШРИХ-КОДУ ПОЛИСА ОМС',
                    'url' => 'site/auth-scanner',
                ],
            ];
            return $this->render('index', compact('btns', 'windowName'));
    }

    /**
     * Авторизация пациента для просмотра талонов и/или записи на прием
     * @return array|string
     */
    public function actionAuth()
    {
        $this->view->title = 'Авторизация пользователя';
        $session = Yii::$app->session;
        $session->remove('patient');
        $session->set('isHome', '1');

        $model = new Auth();

        $request = Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {

            $user = $this->checkUser($model);
            Yii::$app->response->format = Response::FORMAT_JSON;

            if (isset($user) && is_array($user) && isset($user[0]['keyid']) && $user[0]['keyid'] != 0) {
                $lastName = $this->mb_ucfirst(mb_strtolower($model->lastname));
                $firstName = $this->mb_ucfirst(mb_strtolower($model->firstname));
                $secondName = $this->mb_ucfirst(mb_strtolower($model->secondname));

                $patientId = $user[0]['keyid'];
               // $patientPhone = $model->phone;
                $patientDob = $model->dob;

                $patientFullName = $lastName . ' ' . $firstName . ' ' . $secondName;
                $patientShortName = $lastName . ' ' . mb_substr($firstName, 0, 1) . '. ' . mb_substr($secondName, 0, 1) . '.';

                $session->set('patient', [
                    'id' => $patientId,
                    'fullName' => $patientFullName,
                    'shortName' => $patientShortName,
                    'dob' => $patientDob //,
                  //  'phone' => $patientPhone
                ]);
               // print_r($user); die();
                return ['success' => true, 'user' => $user[0]];


            } else {
                return ['success' => false, 'error' => $user[0]];
            }
        }
        return $this->render('auth', compact('model'));
    }



    public function actionAuthNumber()
    {
        $session = Yii::$app->session;
        $session->set('isHome', '1');
        $model = new AuthNumber();

        $request = Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {

            $user = $this->checkPolice($model);
            Yii::$app->response->format = Response::FORMAT_JSON;

            if (isset($user) && is_array($user) && isset($user[0]['keyid']) && $user[0]['keyid'] != 0) {

                $lastName = $this->mb_ucfirst(mb_strtolower($user[0]['lastname']));
                $firstName = $this->mb_ucfirst(mb_strtolower($user[0]['firstname']));
                $secondName = $this->mb_ucfirst(mb_strtolower($user[0]['secondname']));

                $patientId = $user[0]['keyid'];
                // $patientPhone = $model->phone;
                $patientDob = $user[0]['dob'];

                $patientFullName = $lastName . ' ' . $firstName . ' ' . $secondName;
                $patientShortName = $lastName . ' ' . mb_substr($firstName, 0, 1) . '. ' . mb_substr($secondName, 0, 1) . '.';

                $session->set('patient', [
                    'id' => $patientId,
                    'fullName' => $patientFullName,
                    'shortName' => $patientShortName,
                    'dob' => $patientDob //,
                    //  'phone' => $patientPhone
                ]);

                return ['success' => true, 'user' => $user[0]];

            } else {
                return ['success' => false, 'error' => $user[0]];
            }
        }
        return $this->render('auth-number', compact('model'));
    }

    /**
     * Авторизация пациента по сканеру
     * @return string
     * @throws \Exception
     */
    public function actionAuthScanner()
    {
        $session = Yii::$app->session;
        $session->remove('patient');

        $session->set('isHome', '1');
        $spec = $session->get('speciality');
        $doc = $session->get('doctor');
        $rnumb = $session->get('rnumb');

        return $this->render('auth-scanner', compact('spec', 'doc', 'rnumb'));
    }

    /**
     * Выход из системы авторизованного пользователя
     *
     * @param $rnumbid
     *
     * @return string
     */
    public function actionPrint()
    {
        $session = Yii::$app->session;
        $this->layout = 'print';
        $barGenerator = new BarcodeGeneratorSVG();
        $rnumbInfo = $this->getRnumbInfo($session['patientChoice']['rnumbId']);
        $rnumb = isset($rnumbInfo[0]) ? $rnumbInfo[0] : [];

        return $this->render('print', compact('rnumb', 'barGenerator'));
    }

    public function actionNotExist()
    {
        $session = Yii::$app->session;
        $session->set('isHome', '1');

        return $this->render('notExist');
    }

    /**
     * Выход из системы авторизованного пользователя
     *
     * @param $rnumbid
     *
     * @return array
     */
//    public function actionRemoveRnumb($rnumbid)
//    {
//        $rnumbRemove = $this->deleteRnumb($rnumbid);
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        if (isset($rnumbRemove) && is_array($rnumbRemove) && isset($rnumbRemove[0]['err_code']) && $rnumbRemove[0]['err_code'] == 0) {
//            return ['success' => true];
//        } else {
//            return ['success' => false];
//        }
//    }

    /**
     * Выход из системы авторизованного пользователя
     */
//    public function actionExit()
//    {
//        $session = Yii::$app->session;
//        if (Yii::$app->request->isAjax) {
//            $session->remove('patient');
//
//            return json_encode(['success' => true]);
//        } else {
//            $session->remove('patient');
//
//            return $this->redirect(['site/index']);
//        }
//    }

    /**
     * Подтверждение записи на прием
     */

//    public function actionMain() {
//
//        $this->view->title = 'Подтверждение записи';
//        $session = Yii::$app->session;
//        $patient = $session->get('patient');
//
//        $btns = [
//            [
//                'title' => 'ПОДТВЕРДИТЬ ЗАПИСЬ НА ПРИЕМ',
//                'url' => 'record/index',
//            ],
//            [
//                'title' => 'ОТМЕНИТЬ ЗАПИСЬ',
//                'url' => 'cancel/index',
//            ]
//        ];
//        return $this->render('main', compact('btns', 'patient'));
//    }
}
