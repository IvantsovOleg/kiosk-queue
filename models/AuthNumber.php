<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 04.12.2018
 * Time: 15:08
 */

namespace app\models;

use yii\base\Model;

class AuthNumber extends Model
{
    public $police_number;

    public function rules()
    {
        return [
            
            [['police_number'], 'required'],
        ];
    }

}