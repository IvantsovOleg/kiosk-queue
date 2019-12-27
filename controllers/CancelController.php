<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\url;

class CancelController extends AppController
{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $patient = $session->get('patient');

        $rnumbs = $this->getRnumbsByPatient($patient['id']);

        return $this->render('index', compact('rnumbs'));
    }

    public function actionTypeAuth()
    {
        $this->view->title = 'Выбор типа авторизации';
        $windowName = 'ДЛЯ ПРОДОЛЖЕНИЯ НЕОБХОДИМО АВТОРИЗОВАТЬСЯ. ВЫБЕРЕТЕ ТИП АВТОРИЗАЦИИ';
        $session = Yii::$app->session;
        $session->set('isHome', '1');
        $session->set('action', 'cancel');

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
        return $this->render('type-auth', compact('btns', 'windowName'));
    }

    public function actionDelete()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->getRequest();

        $patient = $session->get('patient');

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($request->isAjax) {
            $numbId = $request->post('numbid');

            if (isset($numbId)) {
                $result = $this->deleteRnumb($numbId);
                if (isset($result) && $result[0]) {
                    return ['success' => true, 'error' => ''];
                } else {
                    return ['success' => true, 'error' => ''];
                }
            } else {
                return ['success' => true, 'error' => ''];
            }
        } else {
            return ['success' => false, 'error' => ''];
        }
    }
}
