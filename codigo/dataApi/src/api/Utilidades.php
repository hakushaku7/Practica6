<?php
namespace App\api;

class Utilidades
{
    public static function array2xml(mixed $array, $xml = false)
    {
        if ($array instanceof \JsonSerializable)
            $array = json_decode(json_encode($array), true);

        if ($xml === false) {
            $xml = new \SimpleXMLElement('<result/>');
        }

        foreach ($array as $key => $value) {
            if ($value instanceof \JsonSerializable)
                $var = json_decode(json_encode($array), true);
            else
                $var = $value;
            if (is_array($var)) {
                Utilidades::array2xml($var, $xml->addChild($key));
            } else {
                $xml->addChild($key, (string)$var);
            }
        }

        return $xml->asXML();
    }



}
