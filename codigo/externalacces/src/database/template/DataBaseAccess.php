<?php
namespace ExternalAccess\database\template;
require_once __DIR__ . "/../../general/DataAccess.php";
require_once __DIR__ . "/DatabaseConector.php";
use ExternalAccess\general\DataAccess;
use ExternalAccess\exceptions\{ExternalError, DatabaseError, InconsistencyError, ElementAlreadyExists, ElementNotFound, EntityNotFound};


/**
 * Gestiona el acceso a la información de la base de datos
 * Adaptación de la interfaz {@link DataAcces} para bases de datos.
 * Trabaja con {@link Tabla}
 * Usa implementaciones de la interfaz {@link DataBaseConector} para conectarse a la misma
 * 
 * @author Javier González Conde
 * @version 1.0 
 */
interface DataBaseAccess extends DataAccess
{

    /**
     * Comprueba si un elemento existe en la base de datos basandose 
     * en su id.
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * 
     * @param Tabla $elemento Elemento que queremos comprobar si existe
     * @return bool Verdadero si el elemento existe y falso si no
     * @since 1.0
     */
    public static function check(Tabla $elemento): bool;


    /**
     * Comprueba si es seguro eliminar un elemento.
     * En función de lo establecido en el parametro $throw_errors, devuelve false si no es seguro o lanza la exepción correspondiente
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws ElementNotFound El elemento no existe en la base de datos
     * @throws InconsistencyError Eliminar el elemento generaría problemas de consistencia
     * 
     * @param Tabla $elemento Elemento que queremos eliminar
     * @param bool $throw_errors (por defecto false) En true, al encontrar problemas, los lanza como errores. En false, solo devuelve false al encontrarlos.
     * @return bool Verdadero si es seguro intentar eliminar el elemento de la base de datos. 
     * @since 1.0
     */
    public static function checkSaefty_delete(Tabla $elemento, bool $throw_errors = false): bool;

    /**
     * Comprueba si en la base de datos existe una Tabla concreta.
     * En función de lo establecido en el parametro $throw_errors, devuelve false si no es seguro o lanza la exepción correspondiente.
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * 
     * @param string $elementName nombre de la tabla cuya existencia queremos comprobar
     * @param bool $throw_errors (por defecto false) En true, al encontrar problemas, los lanza como errores. En false, solo devuelve false al encontrarlos.
     * @return bool verdadero si la tabla existe, falso si no.
     * @since 1.0
     */
    public static function checkSaefty_table(string $elementName, bool $throw_errors = false): bool;

    /**
     * Comprueba si es seguro guardar los datos de un elemento en la base de datos
     * En función de lo establecido en el parametro $throw_errors, devuelve false si no es seguro o lanza la exepción correspondiente
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws InconsistencyError Eliminar el elemento generaría problemas de consistencia
     * @throws ElementAlreadyExists Si está intentando guardar información que es incompatible con la base de datos, como por ejemplo un dato duplicado en un campo unico
     * 
     * @param Tabla $elemento Elemento que queremos eliminar
     * @param bool $throw_errors (por defecto false) En true, al encontrar problemas, los lanza como errores. En false, solo devuelve false al encontrarlos.
     * @return bool Verdadero si es seguro intentar eliminar el elemento de la base de datos. 
     * @since 1.0
     */
    public static function checkSaefty_save(Tabla $elemento, bool $throw_errors = false): bool;
}