<?php
namespace App\database;
require_once __DIR__ . "/../../vendor/autoload.php";

//Errores
use ExternalAccess\exceptions\{ElementAlreadyExists, ElementNotFound, EntityNotFound, FormatError, InconsistencyError, UncompletedCode};

//Interfaces de la base de datos
use ExternalAccess\database\template\{DataBaseConector, Tabla};
use ExternalAccess\general\{Filtrable, Elemento};

//Implementaciones de la base de datos

use ExternalAccess\database\implementation\{PDO_lib, DataBasePrototype};


//Tablas de la base de datos y constantes SQL
use App\elements\{MarcaDB, ReservaDB, VehiculoDB};
use App\database\ConcesionarioSQL as SQL;



/**
 * Clase especifica para el proyecto que gestiona la interacción con la base de datos
 */
class ConcesionarioDB implements DataBaseInterface
{
    use DataBasePrototype
    {
        delete as private standardDelete;
        save as private standardSave;
    }

    protected static function con(): DataBaseConector
    {
        return new PDO_lib();
    }

    


    


    public static function delete(Elemento $elemento, bool $supress = true): void
    {
        self::standardDelete($elemento, $supress);
        if($elemento instanceof ReservaDB) self::forzarCoherencia();
    }

    public static function save(Elemento $elemento): int
    {
        $id = self::standardSave($elemento);
        if($elemento instanceof ReservaDB) self::forzarCoherencia();
        else if($elemento instanceof VehiculoDB) self::forzarCoherencia();
        return $id;
    }


    private static function contarVehiculosPorMarca(MarcaDB $elemento): int
    {
        return self::con()::select(SQL::CONTAR_VEHICULOS_POR_MARCA, [$elemento->getID()])[0]["result"];
    }

    public static function getReserva(VehiculoDB $vehiculo): ReservaDB | null
    {
        if(!self::vehiculoReservado($vehiculo)) return null;
        $stm = self::get_object_statement(ReservaDB::placeholder());
        $stm .= SQL::FILTER . SQL::FILTER_VEHICULO;
        return self::con()::select(
                    $stm,
                    [$vehiculo->getID()], 
                    ["Class"=>ReservaDB::placeholder(), "Mode" => \PDO::FETCH_CLASS]
                )[0];
    }

    public static function vehiculoReservado(VehiculoDB $elemento): bool
    {
        return self::con()::select(SQL::VEHICULO_RESERVADO, [$elemento->getID()])[0]["result"] != 0;
    }

    public static function forzarCoherencia(): void
    {
        self::con()::select(SQL::MODIFICAR_EL_CAMPO_RESERVADO_DE_VEHICULO_PARA_MANTENER_LA_COHERENCIA);
    }

    public static function checkSaefty_delete(Tabla $elemento, bool $throw_errors= false): bool
    {
        self::checkSaefty_table($elemento::tableName(), true);
        
        switch($elemento::class)
        {
            case MarcaDB::class:
                if($elemento->getID() <= 0) return True;
                else if(!self::check($elemento)) return True;
                
                $var = self::contarVehiculosPorMarca($elemento) == 0;
                if($throw_errors and !$var) throw new InconsistencyError("No se puede eliminar la marca ya que tiene vehiculos asignados");
                else return $var;
            case ReservaDB::class:
                return true;
            case VehiculoDB::class:
                $var = !self::vehiculoReservado($elemento);
                if($throw_errors and !$var) throw new InconsistencyError("No se puede eliminar la el vehiculo ya que tiene una reserva asignados");
                else return $var;
            default:
                throw new EntityNotFound("La clase " . $elemento::tableName() . " no esta configurada para la base de datos en uso");
        }
    }
    public static function checkSaefty_save(Tabla $elemento, bool $throw_errors = false): bool
    {
        if(!self::checkSaefty_table($elemento::tableName(), $throw_errors)) return false;
        if(!$elemento->isReady())
            if($throw_errors)
                throw new FormatError("El elemento no se puede guardar ya que no cuenta con la información necesaria");
            else
                return false;
        switch($elemento::class)
        {
            case MarcaDB::class:
                return true;
            case ReservaDB::class:
                return self::checkSaefty_reserva($elemento, $throw_errors);
            case VehiculoDB::class:
                return self::checkSaefty_matricula($elemento, $throw_errors);
            default:
                throw new EntityNotFound("La clase " . $elemento::class. " no esta habilitada para el acceso a esta base de datos");
        }
    }

    
    public static function checkSaefty_matricula(VehiculoDB $vehiculo, bool $throw_errors = false): bool
    {
        $var = self::con()::select(SQL::MATRICULA_CONSISTENCIA, [$vehiculo->getID(), $vehiculo->getMatricula()])[0]["result"] == 0;
        if($throw_errors and !$var)
            throw new ElementAlreadyExists("Ya existe un vehiculo con la matricula " . $vehiculo->getMatricula());
        else
            return $var;
    }

    public static function checkSaefty_reserva(ReservaDB $reserva, bool $throw_errors = false): bool
    {
        if($reserva->getVehiculoID() == null) return true;
        $var = self::con()::select(SQL::RESERVA_CONSISTENCIA, [$reserva->getID(), $reserva->getVehiculoID()])[0]["result"] == 0;
        if($throw_errors and !$var)
            throw new ElementAlreadyExists("Ya existe una reserva para el vehiculo indicado");
        else
            return $var;
    }

    public static function check_matricula(string $matricula): bool
    {
        return self::con()::select(SQL::CONTAR_MATRICULAS, [$matricula])[0]["result"];
    }

    public static function find_matricula(string $matricula): VehiculoDB
    {
        if(!self::check_matricula($matricula)) throw new ElementNotFound("La matricula buscada no existe en la base de datos");
        $stm = self::get_object_statement(VehiculoDB::placeholder());
        $stm .= SQL::FILTER . SQL::FILTER_MATRICULA;
        return self::con()::select($stm, [$matricula], ["Mode" => \PDO::FETCH_CLASS, "Class" => VehiculoDB::placeholder()])[0];
        
    }


    public static function getFiltered(Elemento $elemento, array $filters): array
    {
        if(!$elemento instanceof Tabla) throw new FormatError("\$elemento debe ser compatible con bases de datos");

        switch($elemento::class)
        {
            case VehiculoDB::class:
                self::forzarCoherencia();
                $stm = self::get_object_statement(VehiculoDB::placeholder());
                $first = true;
                $params = [];

                if($filters[VehiculoDB::FILTER_MAX_KM] !== null)
                {
                    $stm .= ($first) ? SQL::FILTER : SQL::AND;
                    $first = false;
                    $stm .= SQL::MAX_KM;
                    array_push($params, $filters[VehiculoDB::FILTER_MAX_KM]);
                }
                if($filters[VehiculoDB::FILTER_MARCA] !== null)
                {
                    $stm .= ($first) ? SQL::FILTER : SQL::AND;
                    $first = false;
                    switch($filters[VehiculoDB::FILTER_MARCA])
                    {
                        case -1:
                            $stm .= SQL::MARCA_NOT_NULL;
                            break;
                        case 0:
                            $stm .= SQL::MARCA_NULL;
                            break;
                        default:
                            $stm .= SQL::MARCA_ID;
                            array_push($params, $filters[VehiculoDB::FILTER_MARCA]);
                            break;
                    }
                }
                if($filters[VehiculoDB::FILTER_RESERVA] !== null)
                {
                    $stm .= ($first) ? SQL::FILTER : SQL::AND;
                    $first = false;
                    $stm .= SQL::RESERVA;
                    array_push($params, $filters[VehiculoDB::FILTER_RESERVA]);
                }
                $stm .= SQL::END;

                return self::con()::select($stm, $params, ["Class" => VehiculoDB::placeholder()]);
            default:
                throw new FormatError("La clase " . $elemento::class . " no esta configurada para aplicar filtros");
        }

    }

    public static function get_filtered(Filtrable $elemento, array $filters): array
    {
        throw new UncompletedCode("Este metodo esta en desusuo, usa getFiltered en su lugar");
    }


    

    


}


