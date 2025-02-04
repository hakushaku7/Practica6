<?php
namespace config;
require_once __DIR__ . "/../vendor/autoload.php";

use ExternalAccess\database\implementation\PDO_lib;


PDO_lib::$database = "Concesionario";
PDO_lib::$server = "localhost";
PDO_lib::$user = "root";
PDO_lib::$password = null;
PDO_lib::$driver = "mysql";
