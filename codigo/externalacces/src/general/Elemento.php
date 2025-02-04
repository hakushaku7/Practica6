<?php
namespace ExternalAccess\general;
require_once __DIR__ . "/../../loaders/load_exceptions.php";
use ExternalAccess\exceptions\{ExternalError, FormatError, ElementNotFound, EntityNotFound};


/**
 * Interfaz que representa un grupo o clase de elementos que pueden ser obtenidos desde la fuente externa.
 * Generalmente se requiere el trabajo conjunto con una implementación de la interfaz {@link DataAccess}.
 * 
 * @version 1.0
 * @author Javier González Conde
 */
interface Elemento
{
    /**
     * @return int Valor del identificador numerico autoincremental del elemento. En los elementos que no hayan sido guardados, esté será 0
     * @since 1.0
     */
    public function getID(): int;

    /**
     * Obtiene un identificador legible del elemento en forma de string
     * @return string Nombre o identificador legible del elemento
     * @since 1.0
     */
    public function getName(): string;

    /**
     * Guarda la información del elemento en la fuente externa, creandolo si no existo o sobreescribiendolo si ya existe.
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws FormatError La información contenida no esta correctamente formateada
     * 
     * @return void
     * @since 1.0
     */
    public function save(): void;

    /**
     * Elimina la información del elemento de la fuente externa.
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * 
     * @param int $id identificador del elemento que queremos borrar
     * @return void
     * @since 1.0
     */
    public static function delete(int $id): void;

    /**
     * Busca y devuelve un elemento de la fuente externa
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws ElementNotFound El id buscado no existe en la fuente de datos externa
     * @throws FormatError El id no está formateado correctamente (por ejemplo, es menor o igual a 0)
     * 
     * @param int $id identificador del elemento que queremos obteber
     * @return self elemento buscado
     * @since 1.0
     */
    public static function find(int $id): self;

    /**
     * Obtiene todos los elementos de la clase existentes en la fuente externa
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * 
     * @return array Devuelve un array con todos los elementos de una tabla
     * @since 1.0
     */
    public static function get(): array;

    /**
     * Crea un elemento con información predeterminada de la clase.
     * 
     * @return self Genera un nuevo elemento "vacio". Este elemento no se generará en la fuente externa hasta que no se use en la función {@link Elemento::save()}
     * @since 1.0
     */
    public static function placeholder(): self;

    /**
     * Permite acceder a la información almacenada localmente en el elemento
     * 
     * @return array array asociativo con los valores de las variables del elemento
     * @since 1.0
     */
    public function get_Values(): array;
    //TODO: Cambiar nombre a get_Values()

}