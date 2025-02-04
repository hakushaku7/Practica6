<?php
namespace ExternalAccess\database\template;
require_once __DIR__ . "/../../general/Elemento.php";
use ExternalAccess\general\{Elemento};
use ExternalAccess\exceptions\{DatabaseError, ExternalError, EntityNotFound};
/**
 * Interfaz para asociar una clase con una tabla de una base de datos.
 * Adaptación de {@link Elemento} para las bases de datos.
 * Suele trabajar junto con {@link DataBaseAccess}.
 * 
 * @author Javier González Conde
 * @verison 1.0
 */
interface Tabla extends Elemento
{
     /**
     * Indica el nombre de la tabla de la base de datos a la que se asócia la clase
     * 
     * @return string Nombre de la tabla en la base de datos
     * @since 1.0
     */
    public static function tableName(): string;

    /**
     * Indica la relación existente entre las propiedades del objeto y los campos de la base de datos.
     * No se incluye el id porque se asume que existirá nombrado como id.
     * El resultado se presentara con el siquiente formato:
     * ["nombre de la propiedad" => "nombre del campo"] 
     * @return array Array asociativo con el siguiente formato:
     * @since 1.0
     */
    public static function fields(): array;

    //TODO: ¿Eliminar?
    public static function whipe(self &$elemento): void;

    /**
     * Comprueba si el las propiedades del objeto están correctamente inicializados
     * @return bool si se han inicializado los campos minimos
     * @since 1.0
     */
    public function isReady(): bool;

    /**
     * Comprueba si existe un objeto de está clase guardado en la base de datos correspondiente al id indicado
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws DatabaseError Problema durante la conexión con la base de datos
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * 
     * @param int $id identificador que queremos comprobar si existe en la base de datos 
     * @return bool true si el id existe en la base de datos
     * @since 1.0
     */
    public static function check_ID(int $id): bool;
}