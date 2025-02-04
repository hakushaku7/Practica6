<?php
namespace ExternalAccess\general;
require_once __DIR__ . "/../../loaders/load_exceptions.php";
use ExternalAccess\exceptions\{ExternalError, FormatError, ElementNotFound, EntityNotFound};

/**
 * Interfaz que especifica las funciones genericas de funcionamiento de acceso a la informacción almacenada en una fuente externa, pudiendo ser esta una base de datos, una api...
 * El prerequisito es que en la fuente externa los elementos estén guardados con un identificador (id) numérico autoincremental.
 * Para usar esta interfaz, se require que los elementos implementen la interfaz {@link Elemento}
 * 
 * @verison 1.0
 * @author Javier González Conde
 */
interface DataAccess
{

    /**
     * Guarda la información de un elemento en la fuente externa.
     * Si el elemento no existe, crea uno nuevo. Si ya existe, sobreescribe la información
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws FormatError La información contenida en $elemento no esta correctamente formateada
     * 
     * @param Elemento $elemento cuya información queremos guardar
     * @return int identificador del elemento, util para saber cual ha sido asignado en caso de haber creado un nuevo elemento
     * @since 1.0
     */
    public static function save(Elemento $elemento): int;

    /**
     * Elimina los datos de un elemento de la fuente externa.
     * Lanza errores en caso de que el elemento exista y no pueda ser eliminado
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws ElementNotFound (solo si $supress es false) El elemento que intentas eliminar no existe
     * 
     * @param Elemento $elemento Elemento que queremos eliminar
     * @param bool $supress (por defecto true) Si es falso, lanza un error en caso de que queramos eliminar un elemento que no existe
     * @return void
     * @since 1.0
     */
    public static function delete(Elemento $elemento, bool $supress=true): void;
    
    /**
     * Busca y retorna un elemento de una clase especifica en función de su id
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws ElementNotFound El elemento buscado no existe
     * @throws FormatError La información contenida en $elemento no esta correctamente formateada
     * 
     * @param Elemento $elemento Objeto de la clase que queremos retornar con el id del elemento buscado
     * @return Elemento objeto con la información del elemento buscado
     * @since 1.0
     */
    public static function find(Elemento $elemento): Elemento;
    
    /**
     * Busca y devuelve todas la instancias existentes de un clase en la fuente externa
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws FormatError La información contenida en $elemento no esta correctamente formateada
     * 
     * @param Elemento $elemento Objeto de la clase que queremos retornar
     * @return array que contiene todos los elementos de la clase buscada
     * @since 1.0
     */
    public static function get(Elemento $elemento): array;
}