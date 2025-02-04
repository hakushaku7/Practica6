<?php
namespace ExternalAccess\api\server;

interface ApiElement extends \JsonSerializable
{
    //public static function path():string;

    public function load(array $data);



}