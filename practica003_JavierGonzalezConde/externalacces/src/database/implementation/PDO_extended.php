<?php
namespace ExternalAccess\database\implementation;
require_once __DIR__ . "/PDO_lib.php";
require_once __DIR__ . "/../template/Tabla.php";
use \PDO;
use \PDOException;
use ExternalAccess\exceptions\{DatabaseError, FormatError};



/**
 * Versión de la la clase PDO_lib que utiliza una forma alternativa para cargar los objetos desde la base de datos, evitando problemillas con el FETCH_CLASS y similares
 */
class PDO_extended extends PDO_lib
{
    public const FETCH_CLASS = PDO::FETCH_CLASS;
    public const FETCH_ASSOC = PDO::FETCH_ASSOC;
    public const FETCH_FROM_FUNCT = "Cargar desde funciones";
    
    public static function selectALL(string $consulta, array|null $param = null, $mode = self::FETCH_ASSOC, mixed $extraParam = null ): array
    {
        $con = PDO_lib::openCon();
        try
        {

            $stm = $con->prepare($consulta);
            
            if(!$stm)
                throw new DatabaseError("No se pudo preparar el statement.");
            switch($mode)
            {
                case self::FETCH_CLASS:
                    $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, (is_array($extraParam)
                                        ? $extraParam["Class"]
                                        : $extraParam));
                    break;
                case self::FETCH_FROM_FUNCT:
                    $stm->setFetchMode(PDO::FETCH_ASSOC);
                    break;
                default:
                    $stm->setFetchMode($mode);
                }
           
            if($param != null)
                foreach($param as $key => $value)
                    if(is_int($key))
                        $stm->bindValue($key + 1, $value);
                    else
                        $stm->bindValue($key, $value);
            $stm->execute();
            switch($mode)
            {
                    case self::FETCH_CLASS:
                        return $stm->fetchAll(
                                        PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
                                        (is_array($extraParam) ? $extraParam["Class"] : $extraParam));
                    case self::FETCH_FROM_FUNCT:
                        $class = is_array($extraParam) ? $extraParam["Class"] : $extraParam;
                        if(! ($class instanceof Tabla))
                            throw new FormatError("Para usar el metodo selectAll con FETCH_FROM_FUNCT debes indicar la clase de destino en 'extraParam' o en 'extraParam[\"Class\"]'.\nEsto debes hacerlo utilizando una instancia de la clase destino");
                        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
                        for($i = 0; $i < count($result); $i++)
                        {
                            $var = $class::placeholder();
                            $var->setValues($result[$i]);
                            $result[$i] = $var;
                        }
                        return $result;
                    default:
                        return $stm->fetchAll($mode);
            };


        } catch(PDOException $ex)
        {
            throw new DatabaseError("Problemas al hacer el statement: " . "\n $consulta\n". $ex->getMessage() );
        }
        

        
    }
}

