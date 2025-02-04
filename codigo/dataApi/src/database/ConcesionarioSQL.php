<?php
namespace App\database;

use ExternalAccess\database\implementation\CodigosSQL as SQL;
/**
 * Código SQL especifico para la gestión de la base de datos de concesionario
 */
class ConcesionarioSQL extends SQL
{
    
    public const CONTAR_VEHICULOS_POR_MARCA = "SELECT count(*) AS result FROM vehiculo WHERE marca_id = ?;";


    public const NULIFICAR_MARCA = "UPDATE vehiculo set marca_id = null where marca_id = ?;";

    public const NULIFICAR_VEHICULO = "UPDATE reserva set vehiculo_id = null where vehiculo_id = ?;";
    public const DESTROY_VEHICULO = "DELETE from reserva WHERE vehiculo_id = ?;";

    public const CONTAR_MATRICULAS = "SELECT count(*) AS result FROM vehiculo WHERE matricula = ?;";
    public const VEHICULO_RESERVADO = "SELECT count(*) AS result FROM reserva WHERE vehiculo_id = ?;";
    public const MATRICULA_CONSISTENCIA = "SELECT count(*) AS result FROM vehiculo WHERE id <> ? and matricula = ?;";

    public const RESERVA_CONSISTENCIA = "SELECT count(*) AS result FROM reserva WHERE id <> ? and vehiculo_id = ?;";
    public const FILTER_MATRICULA = " matricula = ? ";

    public const FILTER_VEHICULO = " vehiculo_id = ? ";

    public const MARCA_ID = " marca_id = ? ";
    public const MARCA_NULL = " marca_id IS NULL ";
    public const MARCA_NOT_NULL = " marca_id IS NOT NULL ";
    public const MAX_KM = " kilometros <= ? ";
    public const RESERVA = " reservado = ? ";

    public const MODIFICAR_EL_CAMPO_RESERVADO_DE_VEHICULO_PARA_MANTENER_LA_COHERENCIA = "UPDATE vehiculo as v set v.reservado= (SELECT count(*) from reserva as r WHERE r.vehiculo_id=v.id);";
    
    //________________________________________CONSULTAS COMPLETAS_____________________________
    
    
}

