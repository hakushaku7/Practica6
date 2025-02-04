<?php
namespace ExternalAccess\database\implementation;

use ExternalAccess\general\{Elemento};
use ExternalAccess\database\template\{DataBaseAccess};



trait TablaPrototype
{

    private static abstract function database(): DataBaseAccess;

    /**
     * @return int Valor del id del elemento
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @return string Nombre o identificador legible del elemento
     */
    public abstract function getName(): string;

    /**
     * Guarda la información del elemento en la base de datos, creandolo si no existo o sobreescribiendolo si ya existe
     */
    public function save(): void
    {
        $id = self::database()::save($this);
        $this->id = $id;
    }

    /**
     * @return bool devuelve comprueba si un id existe en la base de datos
     */
    public static function check_ID(int $id): bool
    {
        $var = self::placeholder();
        $var->id = $id;
        return self::database()::check($var);
    }

    //TODO: COMENTAR
    public function exists():bool
    {
        return self::database()::check($this);
    }

    

    /**
     * Elimina el elemento de la base de datos.
     * 
     * @param int $id identificador del elemento que queremos borrar
     */
    public static function delete(int $id): void
    {
        $var = self::placeholder();
        $var->id = $id;
        self::database()::delete($var);
    }

    public static function whipe(Elemento &$elemento): void
    {
        if($elemento::class == self::class)
        {
            self::delete($elemento->getID());
            unset($elemento);
        }
    }



    /**
     * Busca y devuelve un elemento de la base de datos
     * @param int $id identificador del elemento que queremos obteber
     * @return self elemento buscado
     */
    public static function find(int $id): Elemento
    {
        $var = self::placeholder();
        $var->id = $id;
        $var = self::database()::find($var);
        return $var;
    }

    /**
     * 
     * @return array Devuelve un array con todos los elementos de una tabla
     */
    public static function get(): array
    {
        return self::database()::get(self::placeholder());

    }

}