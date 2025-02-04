<?php
namespace App\database;

use App\elements\{VehiculoDB, ReservaDB};
use ExternalAccess\
    {
        database\template\DataBaseAccess,
        general\Filtrador
    };

interface DataBaseInterface extends DataBaseAccess, Filtrador
{

    public static function getReserva(VehiculoDB $vehiculo): ReservaDB|null;

    public static function check_matricula(string $matricula): bool;

    public static function checkSaefty_reserva(ReservaDB $reserva, bool $throw_errors = false): bool;

    public static function forzarCoherencia(): void;

    public static function find_matricula(string $matricula): VehiculoDB;

    
}