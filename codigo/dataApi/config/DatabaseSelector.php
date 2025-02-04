<?php
namespace config;

use App\database\ExtremeConcesionarioDB;
use App\database\DatabaseInterface;

trait DatabaseSelector
{
    private static function database(): DatabaseInterface
    {
        return new ExtremeConcesionarioDB();
    }
}