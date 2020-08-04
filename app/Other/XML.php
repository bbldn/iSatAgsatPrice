<?php

namespace App\Other;

use SimpleXMLElement;

class XML
{
    /**
     * @param $data
     * @param string $name
     * @return SimpleXMLElement
     */
    public static function arrayToXml(array $data, $name = 'data'): SimpleXMLElement
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><${name}></${name}>");

        return static::action($xml, $data);
    }

    /**
     * @param $data
     * @param $xml
     * @return SimpleXMLElement
     */
    protected static function action(SimpleXMLElement $xml, array $data): SimpleXMLElement
    {
        foreach ($data as $key => $value) {
            if (true === is_numeric($key)) {
                $key = "item{$key}";
            }

            if (true === is_array($value)) {
                $subNode = $xml->addChild($key);
                static::arrayToXml($value, $subNode);
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }

        return $xml;
    }
}
