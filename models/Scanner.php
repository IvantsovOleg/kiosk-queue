<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 10.04.2019
 * Time: 12:29
 */

namespace app\models;


use DateTime;
use Yii;

class Scanner
{
    private $username;
    private $password;

    public $baseUrl;

    /**
     * Scanner constructor.
     */
    public function __construct()
    {
        $this->username = Yii::$app->params['policyReaderUser'];
        $this->password = Yii::$app->params['policyReaderPassword'];

        $this->baseUrl = Yii::$app->params['baseUrlScanner'];
    }

    public function getHeaders()
    {
        $currentDate = new DateTime();

        $binaryNonce = $this->generateNonce();
        $created = $currentDate->format('Y-m-d\TH:i:s');

        $passwordDigest = base64_encode(sha1(base64_decode($binaryNonce) . $created . $this->password, true));

        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'WSSE profile="UsernameToken"',
            'X-WSSE' => 'UsernameToken Username="' . $this->username . '", PasswordDigest="' . $passwordDigest . '", Nonce="' . $binaryNonce . '", Created="' . $created .'"'
        ];
    }

    private function generateNonce($length = 8)
    {
        try {
            $bytes = random_bytes($length);
            return base64_encode($bytes);
        } catch (\Exception $e) {
        }
    }
}