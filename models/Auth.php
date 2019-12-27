<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 04.12.2018
 * Time: 15:08
 */

namespace app\models;

use yii\base\Model;

class Auth extends Model
{
    public $lastname;
    public $firstname;
    public $secondname;
    public $dob;
   // public $phone;

    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['lastname', 'firstname', 'secondname', 'dob'], 'required'],
        ];
    }

}