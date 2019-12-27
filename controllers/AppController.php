<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 09.11.2018
 * Time: 10:47
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class AppController extends Controller
{
    /** Расшифровка возможных внутренних поликлиник и отделений в Городской поликлинике №44
     * ВНИМАНИЕ!!! Если менять $sortCode, то делать это только здесь
     */
    public $offices = [
        'office1' => [
            'sortCode' => '001',
            'officeName' => 'ГОРОДСКАЯ ПОЛИКЛИНИКА №44',
        ],
        'office2' => [
            'sortCode' => '002',
            'officeName' => 'ДЕТСКОЕ ПОЛИКЛИНИЧЕСКОЕ ОТДЕЛЕНИЕ №41',
        ],
        'office3' => [
            'sortCode' => '003',
            'officeName' => 'ЖЕНСКАЯ КОНСУЛЬТАЦИЯ №19',
        ],
        'office4' => [
            'sortCode' => '004',
            'officeName' => 'ПОЛИКЛИНИЧЕСКОЕ ОТДЕЛЕНИЕ №82',
        ],
    ];

    const CHECK_POLICE = 'KIOSK_CHECK_BY_POLICE';

    const GET_DEPS = 'KIOSK_GET_DEPS';

    // Список специальностей врачей по выбранному отделению/больнице
    const GET_SPECIALITY = 'RU780004_SPEC_LIST';

    // Список ФИО врачей по выбранному отделению и выбранной специальности
    const GET_DOCTORS = 'RU780004_DOC_LIST';

    // Список доступных талонов по конкретному специалисту
    const GET_RNUMB = 'RU780004_NUMB_LIST';

    // Блокирование выбранного талона перед записью
    const RNUMB_SET_BLSTATUS = 'RU780004_SET_BL_STATUS';

    // Снятие блокировки выбранного талона
    const RNUMB_UNSET_BLSTATUS = 'RU780004_UNSET_BL_STATUS';

    // Проверка пациента
    const CHECK_PATIENT = 'KIOSK_CHECK_PATIENT';
    const CHECK_PATIENT_FOR_SCANNER = 'KIOSK_CHECK_PATIENT_FOR_SCANNER';

    // Запись пациента на прием
    const SUCCESS_APPT = 'KIOSK_APPT_COMPLETE';

    // Талоноы пациента
    const PATIENT_NUMBS_BY_ID = 'KIOSK_PATIENT_NUMBS_BY_ID';

    // Информация по выбранному талону (для печати)
    const RNUMB_INFO = 'KIOSK_RNUMB_INFO';

    // Отмена записи и блокировки на талон
    const RNUMB_CLEAR_STATUS = 'KIOSK_DELETE_RNUMB';

    // Календарь врача
    const CALENDAR = 'KIOSK_GET_CALENDAR';

    public $layout = 'kiosk';

    /**
     * Глобальные правила
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Глобальные actions
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    function mb_ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));

        return $fc . mb_substr($str, 1);
    }

    protected function checkPolice($check_police)
    {
        $params = [
            'p_number' => $check_police->police_number,
            'p_dob' =>'',
            'p_phone' => '',
        ];

        try {
            return $this->runXMLCommand(AppController::CHECK_POLICE, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }
//    protected function checkPolice($auth)
//    {
//        $params = [
//            'p_number' => $auth->police_number,
//            'p_dob' => '',
//            'p_phone' => '',
//        ];
//
//        try {
//            return $this->runXMLCommand(AppController::CHECK_POLICE, $params);
//        } catch (HttpException $e) {
//            die($e);
//        }
//    }
    /**
     * Авторизация пациента по фио дате рождения
     *
     * @param $auth
     *
     * @return array|bool
     */
    protected function checkUser($auth)
    {
        $params = [
            'p_last_name' => $auth->lastname,
            'p_first_name' => $auth->firstname,
            'p_second_name' => $auth->secondname,
            'p_dob' => $auth->dob,
            'p_phone' => '',
        ];

        try {
            return $this->runXMLCommand(AppController::CHECK_PATIENT, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    /**
     * Отправка и получение результата от XML Server
     *
     * @param $command
     * @param array $params
     *
     * @return array|bool
     * @throws HttpException
     */
    protected function runXMLCommand($command, $params = [])
    {
        $result = [];
        $errorMsg = null;

        $paramList = $this->setParams($params);

        Yii::$app->xml->makeRequest($command, $paramList, $result, $errorMsg);

        if (isset($errorMsg)) {
            throw new HttpException(500, $errorMsg);
        }

        if (is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return [];
        }
    }

    /**
     * Установка параметров для выполнения процедуры
     *
     * @param array $params
     *
     * @return array
     */
    private function setParams($params = [])
    {
        $result = [];
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $key => $value) {
                array_push($result, ["NAME" => $key, "VALUE" => $value]);
            }
        }

        return $result;
    }

    protected function checkUserForScanner($auth)
    {
        $params = [
            'p_police' => $auth->police,
            'p_last_name' => $auth->lastname,
            'p_first_name' => $auth->firstname,
            'p_second_name' => $auth->secondname,
            'p_dob' => $auth->dob,
            'p_phone' => $auth->phone,
        ];

        try {
            return $this->runXMLCommand(AppController::CHECK_PATIENT_FOR_SCANNER, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }
    /**
     * Получение специальностей через XML сервер для заданного учреждения
     * для выбранного учреждения (сам запрос в БД создавался Завийским 11.10.2019
     * для проекта "Городская поликлиника №44")
     *
     * @param $sortCode - код отделения/учреждения/больницы
     *
     * @return array|string
     */
    protected function getSpecialityList($sortCode) {
        $params = [
            'SORTCODE' => $sortCode,
        ];
        try {
            return $this->runXMLCommand(AppController::GET_SPECIALITY, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }
    /**
     * Получение списка врачей для переданной специальности и отделения/больницы через XML сервер
     * (сам запрос в БД создавался создавался Гатауллиным 16.10.2019 для проекта "Городская поликлиника №44")
     *
     * @param $specId - ID должности специалиста
     * @param $sortCode - код отделения/учреждения/больницы
     *
     * @return array|integer|string
     */
    protected function getDoctorList($specId, $sortCode) {
        $params = [
            'SPECID' => $specId,
            'SORTCODE' => $sortCode,
        ];
        try {
            return $this->runXMLCommand(AppController::GET_DOCTORS, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    /**
     * Получение списка талонов по конкретному специалисту через XML сервер
     *
     * @param $docId - ID конкретного специалиста
     *
     * @return array|string
     */
    protected function getRnumbs($docId)
    {
        $params = [
            'DOCID' => $docId,
            ];

        try {
            return $this->runXMLCommand(AppController::GET_RNUMB, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    /**
     * Блокировка талона
     *
     * @param $rnumbId
     *
     * @return array|bool
     */
    protected function setBlockRnumb($rnumbId)
    {
        $params = ['P_RNUMB_ID' => $rnumbId];

        try {
            return $this->runXMLCommand(AppController::RNUMB_SET_BLSTATUS, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    /**
     * Снятие блокировки талона
     *
     * @param $rnumbId
     *
     * @return array|bool
     */
    protected function unsetBlockRnumb($rnumbId)
    {
        $params = ['P_RNUMB_ID' => $rnumbId];

        try {
            return $this->runXMLCommand(AppController::RNUMB_UNSET_BLSTATUS, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    public function actionUnsetBlockRnumbWhenGoHome ()
    {
        $rnumbId = Yii::$app->request->post()['rnumbId'];

        $unsetResult = $this->unsetBlockRnumb($rnumbId);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($unsetResult[0]['ERR_CODE'] == 0) {
            return ['success' => true];
        } else {
            return ['success' => false];
        }
    }

    /**
     * Подтверждение записи на прием
     *
     * @param $patientId
     * @param $rnumbId
     *
     * @return array|bool
     */
    protected function complete($patientId, $rnumbId)
    {
        $params = ['P_RNUMB_ID' => $rnumbId, 'P_PATIENT_ID' => $patientId];

        try {
            return $this->runXMLCommand(AppController::SUCCESS_APPT, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    /**
     * Список талонов для указанного пациента
     *
     * @param $patientId
     *
     * @return array|bool
     */
    protected function getRnumbsByPatient($patientId)
    {
        $params = ['PATIENT_ID' => $patientId,];

        try {
            return $this->runXMLCommand(AppController::PATIENT_NUMBS_BY_ID, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    /**
     * Получить рассписание врача
     *
     * @param $specId
     * @param $docId
     * @param $datBgn
     * @param $datEnd
     *
     * @return array|bool
     */
    protected function getCalendar($specId, $docId, $datBgn, $datEnd)
    {
        $params = [
            'P_SPEC_ID' => $specId,
            'P_DOC_ID' => $docId,
            'P_FIRST_DATE' => $datBgn,
            'P_LAST_DATE' => $datEnd,
        ];

        try {
            return $this->runXMLCommand(AppController::CALENDAR, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    /**
     * Вернуть информацию по талону
     *
     * @param $rnumbId
     *
     * @return array|bool
     */
    protected function getRnumbInfo($rnumbId)
    {
        $params = [
            'P_RNUMB_ID' => $rnumbId
        ];

        try {
            return $this->runXMLCommand(AppController::RNUMB_INFO, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }
    protected function getDeps()
    {
        $params = [
        ];

        try {
            return $this->runXMLCommand(AppController::GET_DEPS, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }

    /**
     * Отменить запись на талон и снятие блокировки
     *
     * @param $rnumbId
     *
     * @return array|bool
     */
    protected function deleteRnumb($rnumbId)
    {
        $params = [
            'P_RNUMB_ID' => $rnumbId
        ];

        try {
            return $this->runXMLCommand(AppController::RNUMB_CLEAR_STATUS, $params);
        } catch (HttpException $e) {
            die($e);
        }
    }
}