<?php

namespace app\controllers;

use Yii;
use SoapFault;
use SoapClient;
use yii\web\Response;
use app\models\Scanner;
use yii\web\HttpException;
use yii\httpclient\Client;
use app\models\AuthScanner;

class ReaderController extends AppController
{

    public $enableCsrfValidation = false;

    /**
     * Запуск считывания полиса
     * @return null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionStart()
    {
        $auth = new Scanner();

        $headers = $auth->getHeaders();
        $clientIp = Yii::$app->request->getUserIP();
        $readerUrl = '';

        if (isset($clientIp)) {
            $readerUrl = 'http://' . $clientIp . $this->getReaderUrl();
        }

        $client = new Client(['baseUrl' => $readerUrl]);

        $request = $client->createRequest()
            ->setUrl('policy/read/start')
            ->setMethod('POST')
            ->setFormat(Client::FORMAT_JSON)
            ->setData(null)
            ->setHeaders($headers);

        $response = $request->send();

        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($response->isOk) {
            return $response->data;
        } else {
            return null;
        }
    }

    private function getReaderUrl()
    {
        return ':8080/' . Yii::$app->params['policyReaderUser'] . '/api';
    }

    /**
     * Статус считывания полиса
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionStatus()
    {
        $auth = new Scanner();

        $headers = $auth->getHeaders();

        $clientIp = Yii::$app->request->getUserIP();
        $readerUrl = '';

        if (isset($clientIp)) {
            $readerUrl = 'http://' . $clientIp . $this->getReaderUrl();
        }

        $client = new Client(['baseUrl' => $readerUrl]);

        $request = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl('policy/read/status')
            ->setHeaders($headers);

        $response = $request->send();


        // print_r($response->data);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($response->isOk) {
            return $response->data;
        } else {
            return null;
        }
    }

    /**
     * Информация о сервисе
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionInfo()
    {
        $auth = new Scanner();

        $headers = $auth->getHeaders();

        $clientIp = Yii::$app->request->getUserIP();
        $readerUrl = '';

        if (isset($clientIp)) {
            $readerUrl = 'http://' . $clientIp . $this->getReaderUrl();
        }

        $client = new Client(['baseUrl' => $readerUrl]);

        $request = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl('info')
            ->setHeaders($headers);

        $response = $request->send();

        // print_r($readerUrl); die();

        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($response->isOk) {
            return $response->data;
        } else {
            return null;
        }
    }

    /**
     * Статус считывания полиса
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionGetData()
    {
        $auth = new Scanner();

        $headers = $auth->getHeaders();

        $clientIp = Yii::$app->request->getUserIP();
        $readerUrl = '';

        if (isset($clientIp)) {
            $readerUrl = 'http://' . $clientIp . $this->getReaderUrl();
        }

        $client = new Client(['baseUrl' => $readerUrl]);

        $request = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl('policy')
            ->setHeaders($headers);

        $response = $request->send();

        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($response->isOk) {
            return $response->data;
        } else {
            return null;
        }
    }

    /**
     * Остановка считывания полиса
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionStop()
    {
        $auth = new Scanner();

        $headers = $auth->getHeaders();

        $clientIp = Yii::$app->request->getUserIP();
        $readerUrl = '';

        if (isset($clientIp)) {
            $readerUrl = 'http://' . $clientIp . $this->getReaderUrl();
        }

        $client = new Client(['baseUrl' => $readerUrl]);

        $request = $client->createRequest()
            ->setUrl('policy/read/stop')
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('POST')
            ->setData(null)
            ->setHeaders($headers);

        $response = $request->send();

        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($response->isOk) {
            return $response->data;
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    public function actionSetPolicy()
    {
        $session = Yii::$app->session;
        $session->remove('patientByScanner');

        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        if ($request->isPost) {
            $patient = json_decode($request->getRawBody());

            $patientByScanner = [
                'omsNumber' => $patient->omsNumber,
                'lastName' => $patient->lastName,
                'firstName' => $patient->firstName,
                'secondName' => $patient->secondName,
                'sex' => $patient->sex,
                'birthDate' => $patient->birthDate,
                'beginDate' => $patient->beginDate,
                'expireDate' => $patient->expireDate,
                'ogrn' => $patient->ogrn,
                'okato' => $patient->okato,
                'typeReader' => $patient->typeReader,
                'barCode' => isset($patient->barCode) ? $patient->barCode : null,
                'ekpUid' => isset($patient->ekpUid) ? $patient->ekpUid : null
            ];

            if (isset($patient->ekpUid)) {

                $model = new AuthScanner();
                $patientByEkp = $this->actionSetEkpNumber(trim($patient->ekpUid));

                if (isset($patientByEkp) && !isset($patientByEkp['error'])) {
                    $model->lastname = $patientByEkp['lastName'];
                    $model->firstname = $patientByEkp['firstName'];
                    $model->secondname = $patientByEkp['secondName'];
                    $model->dob = $patientByEkp['birthDate'];
                    $model->police = $patientByEkp['omsNumber'];
                } else {
                    return ['success' => false, 'user_error' => $patientByEkp['error']];
                }

            } else if (isset($patient->barCode)) {
                $session->remove('research');
                if (strpos($patient->barCode, 'M') == 0) {
                   $researchId =  substr(trim($patient->barCode), 1);

                   $research = $this->checkResearchNumber($researchId);

                    if (isset($research) && is_array($research) && isset($research[0]['TALON_ID']) && $research[0]['TALON_ID'] != 0) {
                        $session->set('research', $research[0]);
                        return ['success' => true, 'user' => ''];
                    } else {
                        return ['success' => false, 'user_error' => 'Не удалось найти Ваше направление, пожалуйста обратитесь к оператору'];
                    }
                } else {
                    $model = new AuthScanner();
                    $patientByEkpQr = $this->actionSetEkpQr(trim($patient->barCode));

                    if (isset($patientByEkpQr) && !isset($patientByEkpQr['error'])) {
                        $model->lastname = $patientByEkpQr['lastName'];
                        $model->firstname = $patientByEkpQr['firstName'];
                        $model->secondname = $patientByEkpQr['secondName'];
                        $model->dob = $patientByEkpQr['birthDate'];
                        $model->police = $patientByEkpQr['omsNumber'];
                    } else {
                        return ['success' => false, 'user_error' => $patientByEkpQr['error']];
                    }
                }
            } else {
                $model = new AuthScanner();

                $model->lastname = $patient->lastName;
                $model->firstname = $patient->firstName;
                $model->secondname = $patient->secondName;
                $model->dob = $patient->birthDate;
                $model->police = $patient->omsNumber;
            }

            $user = $this->checkUserForScanner($model);

            if (isset($user) && is_array($user) && isset($user[0]['KEYID']) && $user[0]['KEYID'] != 0) {

                $patientId = $user[0]['KEYID'];
                $lastName = $this->mb_ucfirst(mb_strtolower($user[0]['LASTNAME']));
                $firstName = $this->mb_ucfirst(mb_strtolower($user[0]['FIRSTNAME']));
                $secondName = $this->mb_ucfirst(mb_strtolower($user[0]['SECONDNAME']));
                $patientFullName = $lastName . ' ' . $firstName . ' ' . $secondName;
                $patientShortName = $lastName . ' ' . mb_substr($firstName, 0, 1) . '. ' . mb_substr($secondName, 0, 1) . '.';

                $session->set('patient', ['id' => $patientId, 'fullName' => $patientFullName, 'shortName' => $patientShortName]);
                return ['success' => true, 'user' => $user[0]['KEYID']];
            } else {
                return ['success' => false, 'user_error' => $user[0]['ERR_TEXT']];
            }
        } else {
            return ['success' => false, 'user_error' => 'Допустимы только POST запросы'];
        }
    }

    /**
     * @param $cardUid
     * @return array
     */
    public function actionSetEkpNumber($cardUid)
    {
        $clientSoap = new SoapClient(Yii::$app->params['soapRegizUrl'], [
            'login' => Yii::$app->params['soapRegizLogin'],
            'password' => Yii::$app->params['soapRegizPassword'],
        ]);
        try {
            $result = $clientSoap->Patient([
                'bskNum' => $cardUid
            ]);

        } catch (SoapFault $e) {
            return ['error' => 'Для авторизации необходимо предоставить ЕКП!'];
        }


        if (isset($result) && isset($result->patient)) {
            $response = [
                'omsNumber' => $result->patient->omsNumber,
                'lastName' => $result->patient->lastName,
                'firstName' => $result->patient->firstName,
                'secondName' => $result->patient->middleName,
                'birthDate' => $result->patient->dateOfBirth,
            ];
        } else {
            $response = ['error' => 'Пациент не найден в сервисе получения данных держателя ЕКП для МИС'];
        }

        return $response;
    }

    public function actionSetEkpQr($cardQr)
    {
        $clientSoap = new SoapClient(Yii::$app->params['soapRegizUrl'], [
            'login' => Yii::$app->params['soapRegizLogin'],
            'password' => Yii::$app->params['soapRegizPassword'],
        ]);
        try {
            $result = $clientSoap->Patient([
                'qr' => $cardQr
            ]);
        } catch (SoapFault $e) {
            return ['error' => 'Для авторизации необходимо предоставить ЕКП!'];
        }

        if (isset($result) && isset($result->patient)) {
            $response = [
                'omsNumber' => $result->patient->omsNumber,
                'lastName' => $result->patient->lastName,
                'firstName' => $result->patient->firstName,
                'secondName' => $result->patient->middleName,
                'birthDate' => $result->patient->dateOfBirth,
            ];
        } else {
            $response = ['error' => 'Пациент не найден в сервисе получения данных держателя ЕКП для МИС'];
        }

        return $response;
    }


    /**
     * @param $auth
     * @return array|\Exception|HttpException
     */
    protected function checkUserForScanner($auth)
    {
        $params = [
            'P_POLICE' => $auth->police,
            'P_LAST_NAME' => $auth->lastname,
            'P_FIRST_NAME' => $auth->firstname,
            'P_SECOND_NAME' => $auth->secondname,
            'P_DOB' => $auth->dob,
            'P_PHONE' => $auth->phone,
        ];

        try {
            return $this->runXMLCommand(AppController::CHECK_PATIENT_FOR_SCANNER, $params);
        } catch (HttpException $e) {
            return $e;
        }
    }

    protected function checkResearchNumber($researchId)
    {
        $params = [
            'P_RESEARCH_ID' => $researchId
        ];

        try {
            return $this->runXMLCommand(AppController::ELECTRONICQUEUE_GET_TALON_FOR_RESEARCH, $params);
        } catch (HttpException $e) {
            return $e;
        }
    }

}
