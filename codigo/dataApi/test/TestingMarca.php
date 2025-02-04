<?php
namespace test;
require_once __DIR__ . "/../config/preload.php";



use App\elements\MarcaDB;


$list = MarcaDB::get();

foreach($list as $marca)
{
    echo $marca->getID() . "    " .$marca->getName() . "\n";
    if($marca->getName() == "COCHESCHULOS")
        MarcaDB::delete($marca->getID());
}

$primera = MarcaDB::find(3);
$primera->setNombre("PITO");
print_r($primera);
$primera->save();
print_r($primera);
$segundo = MarcaDB::find($primera->getID());
print_r($segundo);


