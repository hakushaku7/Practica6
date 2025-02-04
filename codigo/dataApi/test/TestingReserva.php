<?php
namespace test;
require_once __DIR__ . "/../config/preload.php";



use App\elements\ReservaDB;

/*
$lista = ReservaDB::get();

foreach($lista as $reserva)
{
    echo json_encode($reserva, JSON_PRETTY_PRINT) . "\n";
}
*/
$var = ReservaDB::find(8);
echo "\n\n\n".json_encode($var, JSON_PRETTY_PRINT) . "\n";
$data = $var->get_Values();
print_r($data);
echo "\n";
$data["vehiculo_id"] = "8";
$var->load(["vehiculo_id" => "8.1"]);
$var->save();



/*
$var = ReservaDB::placeholder();
$var->setNombre("JAcafasinto");
$var->setApellidos("Addddddd");
$var->setDNI("A" . random_int(100000000, 999999999));
$var->setVehiculo(VehiculoDB::find(7));


$var->save();
$lista = ReservaDB::get();
foreach($lista as $reserva)
    echo json_encode($reserva->get_Values(), JSON_PRETTY_PRINT);
*/