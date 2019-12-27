<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 18.03.2019
 * Time: 11:20
 */

namespace app\controllers;


use Yii;
use yii2fullcalendar\models\Event;

class ScheduleController extends AppController
{
    public function actionIndex() {

        $this->view->title = 'Выбор учреждения';
        $session = Yii::$app->session;
        $session->set('isHome', '1');
        $windowName = 'ВЫБЕРЕТЕ УЧРЕЖДЕНИЕ ДЛЯ ПРОСМОТРА ДОСТУПНЫХ СПЕЦИАЛЬНОСТЕЙ';

        $btns = [];
        foreach ($this->offices as $value) {
            array_push($btns,[
                'title' => $value['officeName'],
                'url' => 'schedule/speciality',
                'sortCode' => $value['sortCode'],
            ]);
        }

        return $this->render('index', compact('btns', 'windowName'));
    }

    /**
     * Список доступных специальностей
     * @return string
     */
    public function actionSpeciality($sortCode)
    {
        $session = Yii::$app->session;
        $session->set('isHome', '1');
        $windowName = 'ВЫБЕРЕТЕ СПЕЦИАЛЬНОСТЬ ДЛЯ ПРОСМОТРА ДОСТУПНЫХ СПЕЦИАЛИСТОВ';
        $offices = $this->offices;
        $speciality = $this->getSpecialityList($sortCode);
        $url = 'schedule/doctor';
        return $this->render('speciality', compact('url', 'windowName', 'offices', 'sortCode', 'speciality'));
    }

    /**
     * Список доступных врачей по специальности
     * @param $specId
     * @param $specName
     * @return string
     */
    public function actionDoctor($specId, $specName, $sortCode)
    {
        $session = Yii::$app->session;
        $session->set('isHome', '1');
        $windowName = 'ВЫБЕРЕТЕ СПЕЦИАЛИСТА ДЛЯ ПРОСМОТРА ЕГО РАСПИСАНИЯ';
        $offices = $this->offices;
        $doctors = $this->getDoctorList($specId, $sortCode);
//        print_r($doctors); die;
        $url = 'schedule/calendar';
        return $this->render('doctor', compact('url', 'windowName', 'offices', 'sortCode', 'specId', 'specName', 'doctors'));
    }

    public function actionCalendar($specId, $specName, $docId, $docName)
    {
        $session = Yii::$app->session;
        $session->set('isHome', '1');

        $startDate = date('Y-m-d h:i:s');
        $endDate = date('Y-m-d h:i:s', $this->add_month(strtotime($startDate)));

        $calendar = $this->getCalendar($specId, $docId, $startDate, $endDate);


        $events = [];

        if (isset($calendar) && count($calendar) > 0) {

            foreach ($calendar as $key => $item) {
                $Event = new Event();
                $Event->id = $key;
                $Event->title = $item['DAT_BGN'] . ' - ' . $item['DAT_END'] . ' (' . $item['TALON_COUNT_DAY'] . ' тал.)';
                $Event->start = date('Y-m-d\TH:i:s\Z', strtotime($item['TALON_DATE'] . ' ' . $item['DAT_BGN']));
                $Event->end = date('Y-m-d\TH:i:s\Z', strtotime($item['TALON_DATE'] . ' ' . $item['DAT_END']));

                $events[] = $Event;
            }

        }
//        $session->set('calendar', $calendar); //TODO staFF
//        $session->set('events', $events); //TODO staFF
        return $this->render('calendar', compact('docName', 'specName', 'events', 'calendar'));
    }


    public function add_month($time)
    {
        $d = date('j', $time);  // день
        $m = date('n', $time);  // месяц
        $y = date('Y', $time);  // год

        // Прибавить месяц
        $m++;
        if ($m > 12) {
            $y++;
            $m = 1;
        }

        // Это последний день месяца?
        if ($d == date('t', $time)) {
            $d = 31;
        }
        // Открутить дату до последнего дня месяца
        if (!checkdate($m, $d, $y)) {
            $d = date('t', mktime(0, 0, 0, $m, 1, $y));
        }
        // Вернуть новую дату в TIMESTAMP
        return mktime(0, 0, 0, $m, $d, $y);
    }

}