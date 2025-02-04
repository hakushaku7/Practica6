<?php
namespace ExternalAccess\database\implementation;
require_once __DIR__ . "/../../../loaders/load_database_interface.php";

use ExternalAccess\general\{Elemento};
use ExternalAccess\exceptions\{ExternalError, DataBaseError, FormatError, ElementNotFound, EntityNotFound};
use ExternalAccess\database\template\{Tabla, DataBaseConector};
use ExternalAccess\database\implementation\CodigosSQL as SQL;

/**
 * Implementa parte de las funciones establecidas en {@link DataBaseAcces}.
 * 
 * @author Javier González Conde
 * @version 1.0
 */
trait DatabasePrototype
{
    //________________________METODOS ABSTRACTOS
    /**
     * Devuelve la conexión establecida con la base de datos
     * 
     * @return DataBaseConector
     * @since 1.0
     */
    private static abstract function con(): DatabaseConector;


    //________________________FUNCIONES DE DataBaseAccess
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
    public static function check(Tabla $elemento): bool
    {
        return self::check_ID($elemento::tableName(), $elemento->getID());
    }

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
    public static function checkSaefty_table(string $tableName, bool $throw_errors = false): bool
    {
        $var = self::con()::select(SQL::CHECK_TABLA, [self::con()::$database, $tableName])[0]["result"] != 0;
        if($throw_errors and !$var) throw new EntityNotFound("La tabla $tableName no existe en la base de datos");
        else return $var;
    }



    //_______________________FUNCIONES DE DataAccess
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
    public static function save(Elemento $elemento): int
    {
        //Comprobamos que la tabla existe
        if(! $elemento instanceof Tabla) throw new FormatError("Al usar la función save desde " . self::class . " el parametro \$elemento debe ser instancia de la interfaz Tabla");
        //Comprobamos que es seguro guardar
        self::checkSaefty_save($elemento, true);

        $id = $elemento->getID();
        //Comprobamos si existe el elemento
        if(self::check($elemento))
            //Si el elemento ya existe, se actualiza
            self::actualizar($elemento);
        else
            //Si el elemento no existe, se crea y obtenemos su nuevo id
            $id = self::crear($elemento);
        //Devolvemos el id
        return $id;
    }

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
    public static function delete(Elemento $elemento, bool $supress = true): void
    {
        //Comprueba se la tabla existe
        if(! $elemento instanceof Tabla) throw new FormatError("Al usar la función delete desde " . self::class . " el parametro \$elemento debe ser instancia de la interfaz Tabla");
        //Comprobamos si el elemento existe
        if(!$supress and !self::check($elemento)) throw new ElementNotFound("El elemento que se intenta eliminar no existe");
        //Comprobamos si es seguro eliminar el elemento
        self::checkSaefty_Delete($elemento, true);

        self::con()::select(SQL::BORRAR . $elemento::tableName() . SQL::FILTER . SQL::FILTER_ID, [$elemento->getID()]);
    }

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
    public static function find(Elemento $elemento): Elemento
    {
        //Comprobación de que la tabla existe
        if(!$elemento instanceof Tabla) throw new FormatError("Al usar la función find desde " . self::class . " el parametro \$elemento debe ser instancia de la interfaz Tabla");
        
        //Si el elemento existe
        if(self::check($elemento))
        {
            $stm = self::get_object_statement($elemento);
            $stm .= SQL::FILTER . SQL::FILTER_ID . SQL::END;
            $elemento = self::con()::select($stm, [$elemento->getID()],  ["Class" => $elemento::class])[0];
            return $elemento;
        }
        else
            throw new ElementNotFound("La tabla " . $elemento::class . " no contiene un elemento con el id " . $elemento->getID());
    }

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
    public static function get(Elemento $tabla): array
    {
        //Comprobamos si la tabla existe an la base de datos
        if(! $tabla instanceof Elemento) throw new FormatError("Al usar la función get desde " . self::class . " el parametro \$elemento debe ser instancia de la interfaz Tabla");
        self::checkSaefty_table($tabla::tableName(), true);
        //Generamos un statement para obtener los objetos
        $stm = self::get_object_statement($tabla) . SQL::END;
        //Devolvemos el listado de objetos
        return self::con()::select($stm, [], ["Class" => $tabla::class]);
    }

    //________________________FUNCIONES UTILITARIAS
    /**
     * Comprueba el si existe un elemento de una tabla
     * 
     * @throws EntityNotFound
     * 
     * @param string $tableName Nombre de la tabla en la que se va a buscar el elemento
     * @param int $id identificador que queremos comporbar si existe
     * @return bool True si existe, false si no.
     * @since 1.0
     */
    public static function check_ID(string $tableName, int $id): bool
    {
        //Comprobamos si la tabla existe en la base de datos
        self::checkSaefty_table($tableName, true);
        if($id <= 0) return false;
        //Preparamos el statement
        $stm = SQL::COUNT . SQL::FROM . $tableName . SQL::FILTER . SQL::FILTER_ID;
        //Devolvemos el resultado de la consulta
        return self::con()::select($stm, [$id])[0]["result"] == 1;
    }

    /**
     * Genera una consulta de mySQL para obtener consultas tipo get
     * 
     * @throws EntityNotFound
     * 
     * @param Tabla $tabla Clase de la que queremos obtener un elemento
     * 
     * @return string parte de la consulta SQL
     * @since 1.0
     */
    private static function get_object_statement(Tabla $tabla): string
    {
        //Generamos el statement
        $stm = SQL::SELECT . $tabla::tableName() .".id as id" ;
        //Añadimos los capos de la tabla
        foreach($tabla::fields() as $varName=>$fieldName)
            $stm .= ", " . $tabla::tableName() . "." . $fieldName . SQL::AS . $varName;
        $stm .= SQL::FROM . $tabla::tableName();
        return $stm;
    }

    /**
     * Genera una consulta de mySQL tipo update
     * 
     * @throws EntityNotFound
     * 
     * @param Tabla $tabla Clase en la que queremos modificar datos
     * 
     * @return string parte de la consulta SQL
     * @since 1.0
     */
    private static function update_object_statement(Tabla $tabla): string
    {
        $stm = SQL::UPDATE . $tabla::tableName() . SQL::SET;
        foreach($tabla::fields() as $varName=>$fieldName)
            $stm .= " " . $tabla::tableName() . "." . $fieldName . " = ?,";
        $stm = substr($stm, 0, -1);
        return $stm;
    }

    /**
     * Genera una consulta de mySQL tipo insert
     * 
     * @throws EntityNotFound
     * 
     * @param Tabla $tabla Clase en la que guardar datos
     * 
     * @return string parte de la consulta SQL
     * @since 1.0
     */
    private static function insert_object_statement(Tabla $tabla): string
    {
        if(!self::checkSaefty_table($tabla::tableName()))
            throw new EntityNotFound("La tabla " . $tabla::tableName() . " no existe en la base de datos");
        $stm = SQL::INSERT . $tabla::tableName() . "(id";
        $end = SQL::VALUES . "0";
        $count= 0;
        foreach($tabla::fields() as $varName=>$fieldName)
        {
            $stm .= ", " . $fieldName;
            $end .= ", ?";
        }
        $stm .= ") $end)" . SQL::END;
        return $stm;
    }

    // TODO: Revisar
    private static function find_info(Tabla $elemento, $optionalID = null): array
    {
        if($optionalID != null)
            $id = $optionalID;
        else
            $id = $elemento->getID();
        $stm = self::get_object_statement($elemento);
        $stm .= SQL::FILTER . SQL::FILTER_ID . SQL::END;
        
        if(self::check_ID($elemento::class, $id))
            return self::con()::select($stm, [$elemento::tableName(), $id]);
        else
            throw new ElementNotFound("En la tabla " . $elemento::tableName() . " no existe un elemento con id " . $id);
    }

    /**
     * Summary of actualizar
     * @param Tabla $elemento
     * @return void
     */
    private static function actualizar(Tabla $elemento): void
    {
        $stm = self::update_object_statement($elemento);
        $stm .= SQL::FILTER . SQL::FILTER_ID . SQL::END;
        $pureData = $elemento->get_Values();
        $data = [];
        foreach($elemento::fields() as $key=>$value)
            array_push($data, $pureData[$value]);

        array_push($data, $elemento->getID());
        self::con()::update($stm, $data);
    }

    private static function crear(Tabla $elemento): int
    {
        //TODO: comprobar la integridad referencial
        $stm = self::insert_object_statement($elemento);
        $pureData = $elemento->get_Values();
        $data = [];
        foreach($elemento::fields() as $key=>$value)
            array_push($data, $pureData[$value]);

        return self::con()::insert($stm, $data);
    }










    

    public static function load(Tabla &$elemento, $optionalID = null): void
    {
        if(!self::check_table($elemento::tableName()))
            throw new EntityNotFound("La tabla " . $elemento::tableName() . " no existe en la base de datos");
        $id = 0;
        if(isset($elemento))
            $id = $elemento->getID();
        
        if($optionalID != null)
            $id = $optionalID;
        
        if($id <= 0)
            throw new FormatError("Los ID indicados no son validos");

        $elemento->load_data(self::find_info($elemento, $id));
    }
}
