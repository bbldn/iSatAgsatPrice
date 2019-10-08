<?php

namespace App\Other;

use SimpleXMLElement;

class XML
{
    public static function arrayToXml($data, $name = 'data')
    {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><${name}></${name}>");
        static::_arrayToXml($data, $xml);
        return $xml;
    }

    protected static function _arrayToXml($data, &$xml_data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item' . $key;
            }

            if (is_array($value)) {
                $subNode = $xml_data->addChild($key);
                static::arrayToXml($value, $subNode);
            } else {
                $xml_data->addChild($key, htmlspecialchars($value));
            }
        }
    }
}
