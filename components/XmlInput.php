<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 29.08.2017
 * Time: 16:11
 */

namespace app\components;

use XMLReader;

class XmlInput
{
    public $xr = null;
    public $command = "";
    public $userid = "";
    public $isTable = FALSE;
    public $tableData;
    public $valueData;
    public $errorText = "";
    public $errorCode = "";

    public function __construct($data)
    {
        $this->xr = new XMLReader();
        $this->xr->XML($data, 'utf-8');
    }

    public function __destruct()
    {
        $this->xr->close();
    }

    public function parseXML()
    {
        $name = "";
        while ($this->xr->read()) {
            if (strtoupper($this->xr->name) == "ERROR" && $this->xr->nodeType == XMLReader::ELEMENT) {
                $this->errorCode = $this->xr->getAttribute("code");
                $this->xr->read();
                $this->errorText = $this->xr->value;
                return FALSE;
            }

            if (strtoupper($this->xr->name) == "RESULT" && $this->xr->nodeType == XMLReader::ELEMENT) {
                $this->command = $this->xr->getAttribute("name");
                $this->userid = $this->xr->getAttribute("userid");

            }

            if (strtoupper($this->xr->name) == "ROW" && $this->xr->nodeType == XMLReader::ELEMENT) {

                $this->isTable = TRUE;
                $rownum = $this->xr->getAttribute("num");
                $params = [];
                while (!(($this->xr->nodeType == XMLReader::END_ELEMENT) && ($this->xr->name == "ROW"))) {
                    $this->xr->read();
                    if ($this->xr->nodeType == XMLReader::ELEMENT) {
                        $name = $this->xr->name;
                    }

                    if ($this->xr->nodeType == XMLReader::TEXT) {
                        $value = $this->xr->value;
                        $params[$name] = $value;
                    }
                }

                $tableResult[$rownum] = $params;
                $params = null;

            } else {
                if ($this->xr->nodeType == XMLReader::ELEMENT)
                    $name = $this->xr->name;

                if ($this->xr->nodeType == XMLReader::TEXT) {
                    $value = $this->xr->value;
                    $dataResult[$name] = $value;
                }
            }
        }

        if (isset($tableResult)) {
            $this->tableData = $tableResult;
        }

        if (isset($dataResult)) {
            $this->valueData = $dataResult;
        }
        return TRUE;
    }
}