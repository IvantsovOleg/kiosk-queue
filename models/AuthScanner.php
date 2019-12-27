<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 04.12.2018
 * Time: 15:08
 */

namespace app\models;

class AuthScanner extends Auth
{
    public $lastname;
    public $firstname;
    public $secondname;
    public $dob;
    public $phone;
    public $police;

    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['lastname', 'firstname', 'secondname', 'dob', 'police'], 'required'],
        ];
    }

}