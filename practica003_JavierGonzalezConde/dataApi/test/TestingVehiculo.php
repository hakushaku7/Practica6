<?php
namespace test;
require_once __DIR__ . "/../config/preload.php";



use App\elements\VehiculoDB;
/*
$lista = VehiculoDB::get();
foreach($lista as $vehiculo)
    echo json_encode($vehiculo->get_Values(), JSON_PRETTY_PRINT);

$var = VehiculoDB::placeholder();
$var->setMatricula("AAA" . random_int(1000, 9999));
$var->setMarca(random_int(1, 5));
$var->setKilometros(random_int(0, 1000000000));
$var->setModelo("Camion");
$var->setColor("Verde");
$var->save();
echo json_encode($var->get_Values(), JSON_PRETTY_PRINT);

*/

$lista = VehiculoDB::get_filtered([VehiculoDB::FILTER_MARCA => "0"]);
foreach($lista as $vehiculo)
    echo json_encode($vehiculo->get_Values(), JSON_PRETTY_PRINT);/**/
