<?php
namespace app\components;

use Yii;
use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 24.07.2017
 * Time: 10:16
 */
class XmlComponent extends Component
{
    public $ip;

    public function init(){
        parent::init();
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public static function getXmlServerFromMySql()
    {
        return Yii::$app->params['XMLServer'];
    }

    public static function fwrite_stream($fp, $string)
    {
        for ($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($fp, substr($string, $written));
            if ($fwrite === false) {
                return $written;
            }
        }
        return $written;
    }

    public static function fread_stream($fp)
    {
        $line = '';
        while (strpos($line, "\r\n") === FALSE)
            $line .= fgets($fp, 4096);
        return $line;
    }

    public static function fixXML($data)
    {
        while (strpos($data, "\r") === 0) $data = substr($data, 1);
        while (strpos($data, "\n") === 0) $data = substr($data, 1);
        return $data;
    }

    public static function makeRequest($command, $params, &$result, &$errorMsg)
    {
        $xmlServerLocalIp = self::getXmlServerFromMySql();

        $XMLCmd = new XmlOutput();
        $XMLCmd->startXML();
        if (isset($_SESSION['USERGROUP'])) {
            if ($_SESSION['USERGROUP'] != 2) {
                $XMLCmd->elementStart("CMD", ["name" => $command, "userid" => '0000+' . $_SESSION['USERID']]);
            } else {
                $XMLCmd->elementStart("CMD", ["name" => $command]);
            }
        } else {
            $XMLCmd->elementStart("CMD", ["name" => $command]);
        }


        for ($i = 0; $i < count($params); $i++)
            $XMLCmd->element("PARAM", array("name" => $params[$i]["NAME"]), $params[$i]["VALUE"]);

        $XMLCmd->elementEnd("CMD");
        $xmlData = $XMLCmd->endXML();

        $fp = @stream_socket_client($xmlServerLocalIp, $errno, $errstr, 10);

        if (!$fp)
            return FALSE;
        else
            self::fwrite_stream($fp, $xmlData . "\r\n");

        $XMLResult = self::fread_stream($fp);
        fclose($fp);

        self::fixXML($XMLResult);

        $xmlInput = new XmlInput($XMLResult);
        $xmlInput->parseXML();

        if ($xmlInput->isTable == FALSE && $xmlInput->errorText != "") {
            $errorMsg = $xmlInput->errorText;
            return FALSE;
        }

        if ($xmlInput->isTable)
            $result = $xmlInput->tableData;
        else
            $result = $xmlInput->valueData;
        return TRUE;
    }
}