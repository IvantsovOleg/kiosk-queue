<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class RecordController extends AppController {

    /**
     * Список доступных специальностей в выбранном учреждении для записи из киоска:
     *
     * @param $sortCode - код учреждения, получаемый из адресной строки;
     *
     * @return $url: string - адрес следующего перехода;
     * @return $windowName: string - название следующегно для отображения на экране;
     * @return $offices: array - названия возможных учреждений;
     * @return $sortCode: string - код текущего учреждения;
     * @return $speciality: array - массив с названиями доступных специальностей и количеством свободных номерков;
     */
    public function actionSpeciality() {

        unset($_SESSION['patientChoice']['specId']);
        unset($_SESSION['patientChoice']['specName']);
        $_SESSION['isHome'] = 1;

        $this->view->title = 'Выбор специальности врача';
//        $session = Yii::$app->session;
//        $session->set('isHome', '1');

        $windowName = 'ПОЖАЛУЙСТА, ВЫБЕРЕТЕ СПЕЦИАЛЬНОСТЬ';
        $offices = $this->offices;
        $speciality = $this->getSpecialityList($_SESSION['patientChoice']['sortCode']);
        $url = 'record/doctor';

        return $this->render('speciality', compact('speciality', 'url', 'windowName', 'offices'));
    }

    /**
     * Список доступных врачей по специальности для записи из киоска:
     *
     * @param $sortCode - код учреждения, получаемый из адресной строки;
     * @param $specId - ID специальности врача, получаемый из адресной строки;
     * @param $specName - название специальности, получаемое из адресной строки;
     *
     * @return $url: string - адрес следующего перехода;
     * @return $windowName: string - название следующегно окна для отображения на экране;
     * @return $offices: array - названия возможных учреждений;
     * @return $sortCode: string - код текущего учреждения;
     * @return $specId: string - ID специальности врача;
     * @return $specName: string - название специальности, получаемое из адресной строки;
     * @return $doctors: array - масссив с конкретными ФИО специалистов и количестквом доступных номерков;
     */
    public function actionDoctor($specId, $specName) {

        unset($_SESSION['patientChoice']['docId']);
        unset($_SESSION['patientChoice']['docName']);
        $_SESSION['isHome'] = 1;

        $this->view->title = 'Выбор специалиста';
        $_SESSION['patientChoice']['specId'] = $specId;
        $_SESSION['patientChoice']['specName'] = $specName;

        $windowName = 'ПОЖАЛУЙСТА, ВЫБЕРЕТЕ СПЕЦИАЛИСТА';
        $doctors = $this->getDoctorList($specId, $_SESSION['patientChoice']['sortCode']);
        $url = 'record/rnumb';

        return $this->render('doctor', compact('doctors', 'url', 'windowName'));
    }

    /**
     * Список доступных талонов для конктретного специалсита с указанием общего кличества доступных талонов для киоска
     *
     * @param $sortCode - код учреждения, получаемый из адресной строки;
     * @param $specId - ID специальности врача, получаемое из адресной строки;
     * @param $specName - название специальности, получаемое из адресной строки;
     * @param $docId - ID конкретного специалиста, получаемый из адресной строки;
     * @param $docName - ФИО конкретного специалиста
     *
     * @return $url: string - адрес следующего перехода;
     * @return $windowName: string - название следующегно окна для отображения на экране;
     * @return $offices: array - названия возможных учреждений;
     * @return $sortCode: string - код текущего учреждения;
     * @return $specName: string - название специальности;
     * @return $docName: string - ФИО конкретного специалиста;
     */
    public function actionRnumb($docId, $docName)
    {
        unset($_SESSION['patientChoice']['rnumbId']);
        unset($_SESSION['patientChoice']['rnumbInfo']);

        $_SESSION['patientChoice']['docId'] = $docId;
        $_SESSION['patientChoice']['docName'] = $docName;
        $_SESSION['isHome'] = 1;

        $this->view->title = 'Выбор времени приема';

        $windowName = 'ПОЖАЛУЙСТА, ВЫБЕРЕТЕ УДОБНУЮ ДЛЯ ВАС ДАТУ И ВРЕМЯ';
        $url = 'site/choose-type-auth';
        $numbList = $this->getRnumbs($docId);

        // Группировка полученных данных по дням
        $i = 0;
        $days = array();
        $date = $dateBegin = $numbList[0]['DD'];
        foreach ($numbList as $index => $numb) {
            if ($date == $numb['DD']) {
                $days[$date]['DD'] = $numb['DD'];
                $days[$date]['DW'] = $numb['DW'];
                if ($dateBegin == $numb['DD']) {
                    $days[$date]['rnumbs'][$i] = $numb;
                } else {
                    $days[$date]['rnumbs'][$i + 1] = $numb;
                }
                $i++;
            } else {
                $i = 0;
                $date = $numb['DD'];
                $days[$date]['DD'] = $numb['DD'];
                $days[$date]['DW'] = $numb['DW'];
                $days[$date]['rnumbs'][$i] = $numb;
            }
        }
        // Получение самой ранней даты
        foreach (array_slice($days, 0,1, true) as $key => $value) {
            $minDate = date('Y-m-d',strtotime($key));
            $firstDate = $key;
            break;
        }
        // Возвращение самой поздней даты
        foreach (array_slice($days, count($days)-1,1, true) as $key => $value) {
            $maxDate = date('Y-m-d',strtotime($key));
            break;
        }

        if (Yii::$app->request->isAjax) {
            if (isset(Yii::$app->request->post()['rnumbId'])) {
                $rnumbId = Yii::$app->request->post()['rnumbId'];
                $rnumbBlock = $this->setBlockRnumb($rnumbId);
                Yii::$app->response->format = Response::FORMAT_JSON;
                if ($rnumbBlock[0]['ERR_CODE'] == 0) {
                    return ['success' => true];
                } else {
                    return ['success' => false];
                }
            }
        }
        return $this->render('day', compact('days', 'firstDate', 'minDate', 'maxDate', 'url', 'windowName'));
    }

    /**
     * Запись на прием пациента
     * @return array|string
     */
    public function actionSuccess()
    {
//        $session = Yii::$app->session;
//        $session->remove('isHome');
        unset($_SESSION['isHome']);

        $patientId = $_SESSION['patient']['id'];
        $rnumbId = $_SESSION['patientChoice']['rnumbId'];

        if (Yii::$app->request->isAjax) {
            //Если это подтверждение записи - записываем пациента
            If (Yii::$app->request->post()['action'] == 'confirm') {
                $success = $this->complete($patientId, $rnumbId);
                Yii::$app->response->format = Response::FORMAT_JSON;
                if (isset($success) && is_array($success) && isset($success[0]['err_code']) && $success[0]['err_code'] == 0) {
                    return ['success' => true];
                } else {
                    return ['success' => false];
                }
            }
            //Если это отмена записи - снимаем блокировку с номерка
            If (Yii::$app->request->post()['action'] == 'cancel') {
                $rnumbBlockUnset = $this->unsetBlockRnumb($rnumbId);
                Yii::$app->response->format = Response::FORMAT_JSON;
                if ($rnumbBlockUnset[0]['ERR_CODE'] == 0) {
                    return ['success' => true];
                } else {
                    return ['success' => false];
                }
            }
        }
        return $this->render('success');
    }

//    public function generateNonce($length = 8)
//    {
//        try {
//            $bytes = random_bytes($length);
//            return base64_encode($bytes);
//        } catch (\Exception $e) {
//        }
//    }
}
