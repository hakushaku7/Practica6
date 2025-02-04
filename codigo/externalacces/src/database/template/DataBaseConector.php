<?php
namespace ExternalAccess\database\template;
require __DIR__ . "/../../../loaders/load_exceptions.php";
use ExternalAccess\exceptions\{DatabaseError};

/**
 * Interfaz que sirve para gestionar la conexión de la base de datos con las implementaciones de {@link DataBaseAcces}.
 * De esta forma, es posible utilizarlas con baseses de datos con distintas lenguajes o sistemas, siempre que cumplan ciertos requisitos minimos.
 * 
 * @author Javier González Conde
 * @version 1.0
 */
interface DataBaseConector
{
    /**
     * Realiza una consulta para obtener información de la base de datos
     * 
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * 
     * @param string $consulta Código que especifica los detalles de la consulta
     * @param array|null $param Parametros que se incluirán en la consulta
     * @param array $extraParam Parametros extra que modifican como se devolvera el resultado, como especificar la clase en la que se cargarán los datos
     * @return array Resultado de la consulta estructurado en un array
     * @since 1.0
     */
    public static function select(string $consulta, array|null $param = null, array $extraParam=[]): array;

    /**
     * Realiza una consulta para guardar nueva información en la base de datos. 
     * 
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * 
     * @param string $consulta Código que especifica los detalles de la consulta
     * @param array|null $param Parametros que se incluirán en la consulta
     * @return int identificador asignado al nuevo objeto creado
     * @since 1.0
     */
    public static function insert(string $consulta, array|null $param = null): int;

    /**
     * Realiza una consulta para sobreescribir información en la base de datos. 
     * 
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * 
     * @param string $consulta Código que especifica los detalles de la consulta
     * @param array|null $param Parametros que se incluirán en la consulta
     * @return void
     * @since 1.0
     */
    public static function update(string $consulta, array|null $param = null): void;

    /**
     * Realiza una consulta para borrar información en la base de datos. 
     * 
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * 
     * @param string $consulta Código que especifica los detalles de la consulta
     * @param array|null $param Parametros que se incluirán en la consulta
     * @return void
     * @since 1.0
     */
    public static function delete(string $consulta, array|null $param = null): void;

    /**
     * Comienza una transacción en la base de datos 
     * 
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * 
     * @return void
     * @since 1.0
     */
    public static function transaction(): void;

     /**
     * Guarda los cambios realizados durante una transacción en la base de datos
     * 
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * 
     * @return void
     * @since 1.0
     */
    public static function commit(): void;

     /**
     * Cancesa los cabios realizados durante una transacción en la base de datos
     * 
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * 
     * @return void
     * @since 1.0
     */
    public static function rollback(): void;

}