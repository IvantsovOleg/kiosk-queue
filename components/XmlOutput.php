<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 29.08.2017
 * Time: 16:11
 */

namespace common\components;

namespace app\components;

use XMLWriter;

class XmlOutput
{

    public $xw = null;

    public function __construct()
    {
        $this->xw = new XMLWriter();
        $this->xw->openMemory();
        $this->xw->setIndent(false);
    }

    public function startXML()
    {
        $this->xw->startDocument('1.0', 'utf-8');
    }

    public function endXML()
    {
        $result = $this->xw->outputMemory(true);
        $this->xw->endDocument();
        return $result;
    }

    public function element($tag, $attrs = null, $content = null)
    {
        $this->elementStart($tag, $attrs);
        if (!is_null($content)) {
            $this->xw->text($content);
        }
        $this->elementEnd($tag);
    }

    public function elementStart($tag, $attrs = null)
    {
        $this->xw->startElement($tag);
        if (is_array($attrs)) {
            foreach ($attrs as $name => $value) {
                $this->xw->writeAttribute($name, $value);
            }
        } elseif (is_string($attrs)) {
            $this->xw->writeAttribute('class', $attrs);
        }
    }

    public function elementEnd($tag)
    {
        static $empty_tag = ['base', 'meta', 'link', 'hr', 'br', 'param', 'img', 'area', 'input', 'col'];
        if (in_array($tag, $empty_tag)) {
            $this->xw->endElement();
        } else {
            $this->xw->fullEndElement();
        }
    }

    public function text($txt)
    {
        $this->xw->text($txt);
    }

    public function raw($xml)
    {
        $this->xw->writeRaw($xml);
    }

    public function comment($txt)
    {
        $this->xw->writeComment($txt);
    }
}