<?php
/*
 * Carga las clases genéricas para el uso de bases de datos
 * requiere configurar la clase DatabaseConfiguration
 */
require_once __DIR__ . "/load_database_interface.php";
require_once __DIR__ . "/../src/database/implementation/DatabasePrototype.php";
require_once __DIR__ . "/../src/database/implementation/TablaPrototype.php";