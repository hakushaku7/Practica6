<?php
namespace test;
require_once __DIR__ . "/../config/preload.php";
use App\elements\{MarcaDB, VehiculoDB, ReservaDB};


function show($elemento)
{
    echo json_encode([$elemento::tableName() => $elemento->getName(), "values" => $elemento->get_Values()], JSON_PRETTY_PRINT) . "\n";
}





//Creación de marca
$marca = MarcaDB::placeholder();
$marca->setNombre("Marca de pruebas");
try
{
    $marca->save();
    echo "Se pudo guardar la marca\n";
    show($marca);
} catch(\Exception $e)
{
    echo "No se pudo guardar la marca\n";
}
try
{
    $marca->setNombre("nuevo nombre");
    $marca->save();
    echo "Se pudo guardar la marca\n";
    show($marca);
} catch(\Exception $e)
{
    echo "No se pudo guardar la marca\n";
}

$vehiculo = VehiculoDB::placeholder();
$vehiculo->setMatricula("AAA" . random_int(1000, 9999));
$vehiculo->setKilometros(random_int(0, 1000000000));
$vehiculo->setModelo("Camion");
$vehiculo->setColor("Verde");
try
{
    $vehiculo->setMarca($marca);
    $vehiculo->save();
    echo "Se pudo guardar el vehiculo\n";
    show($vehiculo);
}catch(\Exception $e)
{
    echo "No se pudo guardar el vehiculo\n";
}
try
{
    $vehiculo->setColor("nuevo color");
    $vehiculo->save();
    echo "Se pudo guardar el vehiculo\n";
    show($vehiculo);
}catch(\Exception $e)
{
    echo "No se pudo guardar el vehiculo ". $e->getMessage() . "\n";
}

$reserva = ReservaDB::placeholder();
$reserva->setNombre("JAcafasinto");
$reserva->setApellidos("Addddddd");
$reserva->setDNI("A" . random_int(100000000, 999999999));
try
{
    $reserva->setVehiculo($vehiculo);
    $reserva->save();
    echo "Se pudo guardar la reserva\n";
    show($reserva);
}catch(\Exception $e)
{
    echo "No se pudo guardar el reserva\n";
}
try
{
    $reserva->setNombre("Nuevo nombre");
    $reserva->save();
    echo "Se pudo guardar la reserva\n";
    show($reserva);
}catch(\Exception $e)
{
    echo "No se pudo guardar el reserva\n";
}

$marca = MarcaDB::find($marca->getID());
show($marca);
$vehiculo = VehiculoDB::find($vehiculo->getID());
show($vehiculo);
$reserva = ReservaDB::find($reserva->getID());
show($reserva);

ReservaDB::whipe($reserva);
VehiculoDB::whipe($vehiculo);
MarcaDB::whipe($marca);