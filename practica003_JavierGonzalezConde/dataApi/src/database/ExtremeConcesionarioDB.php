<?php

namespace App\database;

use ExternalAccess\exceptions\{ElementNotFound, EntityNotFound};
use ExternalAccess\general\Elemento;
use App\elements\{MarcaDB, VehiculoDB, ReservaDB};
use App\database\ConcesionarioSQL as SQL;

class ExtremeConcesionarioDB extends ConcesionarioDB
{
    public static function delete(Elemento $elemento, bool $supress=true): void
    {
        if(!self::check($elemento) and !$supress) throw new ElementNotFound("El elemento que intentas borrar no existe");
        switch($elemento::class)
        {
            case MarcaDB::class:
                self::con()::update(SQL::NULIFICAR_MARCA, [$elemento->getID()]);
                parent::delete($elemento, $supress);
                break;
            case VehiculoDB::class:
                self::con()::update(SQL::DESTROY_VEHICULO, [$elemento->getID()]);
                parent::delete($elemento, $supress);
                break;
            case ReservaDB::class:
                parent::delete($elemento, $supress);
                break;
            default:
                new EntityNotFound("La clase " . $elemento::class . " no esta configurada para el acceso a la base de datos");
        }
    }

   
}