<?php
namespace ExternalAccess\database\implementation;
require_once __DIR__ . "/../template/DataBaseConector.php";
use ExternalAccess\exceptions\{DataBaseError, FormatError};
use ExternalAccess\database\template\DataBaseConector;
use \PDO;
use \PDOException;

/**
 * Instancia de la interfaz {@link DataBaseConector} para trabajar con la librería PDO
 * 
 * @author Javier González Conde
 * @version 1.0
 */
class PDO_lib implements DatabaseConector
{
    /**
     * Objeto estatico que contiene la conexión con la vase de datos.
     * 
     * @var PDO
     * @since 1.0
     */
    static private PDO $con;

    /**
     * Nombre de la base de datos a la que se accede
     * 
     * @var string
     * @since 1.0
     */
    public static string $database = "";

    /**
     * Nombre o dirección del servidor en el que se encuentra la base de datos.
     * Por defecto, 'localhost'
     * 
     * @var string
     * @since 1.0
     */
    public static string $server = "localhost";

    /**
     * Nombre del usuario desde el que se accede a la base de datos
     * Por defecto, 'root'
     * 
     * @var string
     * @since 1.0
     */
    public static string $user = "root";

    /**
     * Contraseña de acceso a la base de datos.
     * Por defecto, sin contraseña (null)
     * 
     * @var string
     * @since 1.0
     */
    public static string|null $password = null;

    /**
     * Driver para la conexión con la base de datos
     * Por defecto, 'mysql'
     * 
     * @var string
     * @since 1.0
     */
    public static string $driver = "mysql";

    /**
     * Devuelve (o genera si no existe) la conexión con la base de datos
     * 
     * @throws DataBaseError no se ha podido conectar con la base de datos
     * 
     * @return PDO Conexión con la base de datos
     * @since 1.0
     */
    protected static function openCon(): PDO
    {
        if(isset(PDO_lib::$con))
            return PDO_lib::$con;
        try
        {
            $dsn = PDO_lib::$driver . ":host=" . PDO_lib::$server .";dbname=" . PDO_lib::$database;

            $conexion = new PDO($dsn, PDO_lib::$user, PDO_lib::$password,
                array(PDO::ATTR_PERSISTENT => true));

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            PDO_lib::$con = $conexion;
            return PDO_lib::$con;

        }
        catch(PDOException)
        {
            throw new DatabaseError("No se ha podido conectar con el servidor");
        }
       
    }

    /**
     * Cierra la conexión con la base de datos
     * 
     * @throws DataBaseError
     * 
     * @return void
     * @since 1.0
     */
    public static function closeCon(): void
    {
        try
        {
            if(isset(PDO_lib::$con))
                unset($conexion);
        }
        catch(PDOException)
        {
            throw new DatabaseError("No se ha podido cerrar la conexion");
        }
    }
    /**
     * Devuelve un objeto PDOStatement con la consulta realizada
     * 
     * @throws DataBaseError Problema al acceder a la base de datos
     * @throws FormatError Los parametros introducidos no son validos
     * 
     * @param string $consulta Código que especifica los detalles de la consulta
     * @param array|null $param Parametros que se incluirán en la consulta
     * @param $mode Modo de retorno del resultado
     * @param array $extraParam Parametros extra que modifican como se devolvera el resultado, como especificar la clase en la que se cargarán los datos
     * @return \PDOStatement Resultado de la consulta
     * @since 0.9
     * @deprecated Usar {@link PDO_lib::select()} en su lugar
     */
    public static function selectSTM(string $consulta, array|null $param = null, $mode = PDO::FETCH_ASSOC, mixed $extraParam = null ): PDOStatement
    {
        $con = PDO_lib::openCon();
        try
        {
            $stm = $con->prepare($consulta);
            if(!$stm)
                throw new DatabaseError("No se pudo preparar el statement: " . $consulta);
            switch($mode)
            {
                case PDO::FETCH_CLASS:
                    $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, (is_array($extraParam)
                                        ? $extraParam["Class"]
                                        : $extraParam));
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
            return match($mode)
                {
                    default=>$stm
                };


        } catch(PDOException $ex)
        {
            throw new DatabaseError("Problemas al hacer el statement: " . $consulta);
        }
    }

    /**
     * @extends
     * 
     * @throws DataBaseError Problema al acceder a la base de datos
     * @throws FormatError Los parametros introducidos no son validos
     * 
     * @param string $consulta Código que especifica los detalles de la consulta
     * @param array|null $param Parametros que se incluirán en la consulta
     * @param array $extraParam Parametros extra que modifican como se devolvera el resultado, como especificar la clase en la que se cargarán los datos
     * @return array Resultado de la consulta estructurado en un array
     * @since 1.0
     */
    public static function select(string $consulta, array|null $param = null, array $extraParam = [] ): array
    {
        //Establecemos los parametros por defecto
        $con = PDO_lib::openCon();
        $mode = PDO::FETCH_ASSOC;
        $class = null;

        //Comprobamos si se ha indicado una clase de retorno
        if(isset($extraParam["Class"]))
        {
            $mode = PDO::FETCH_CLASS;
            $class = (is_string($extraParam["Class"])) ? $extraParam["Class"] : $extraParam["Class"]::class;
        }

        //Comprobamos si se ha indicado un modo 
        if(isset($extraParam["Mode"]))
            $mode = $extraParam["Mode"];

        try
        {
            //Preparamos el statement
            $stm = $con->prepare($consulta);
            if(!$stm)
                throw new DatabaseError("No se pudo preparar el statement: " . $consulta);
            
            //Configuramos el metodo de retorno
            switch($mode)
            {
                case PDO::FETCH_CLASS:
                    if(!isset($extraParam["Class"]))
                        throw new FormatError('Al hacer un select con FETCH_CLASS, debes indicar la clase de destino en el array de $extraParam["Class"]');
                    $stm->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class);
                    break;
                default:
                    $stm->setFetchMode($mode);
            }

            //Añadimos los parametros establecidos
            if($param != null)
                foreach($param as $key => $value)
                    if(is_int($key))
                        $stm->bindValue($key + 1, $value);
                    else
                        $stm->bindValue($key, $value);
            
            //Ejecutamos el statemnt
            $stm->execute();
            //Devolvemos el resultado obtenido
            return match($mode)
                {
                    PDO::FETCH_CLASS=> $stm->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class),
                    default=>$stm->fetchAll($mode)
                };
        }
        catch(PDOException $ex)
        {
            throw new DatabaseError("Problemas al hacer el statement: " . "\n $consulta\n". $ex->getMessage() );
        } 
    }

    /**
     * @extends 
     * 
     * @throws DataBaseError
     * 
     * @param string $consulta Consulta con el formato para añadir la nueva información
     * @param array|null $param Parametros con la información a añadir
     * @return int identificador asignado al nuevo objeto creado
     * @since 1.0
     */
    public static function insert(string $consulta, array|null $param = null): int
    {
        $con = PDO_lib::openCon();
        try
        {
            $stm = $con->prepare($consulta);
            if(!$stm)
                throw new DatabaseError("No se pudo preparar el statement: " . $consulta);
            if($param != null):
                foreach($param as $key => $value)
                    if(is_int($key))
                        $stm->bindValue($key + 1, $value);
                    else
                        $stm->bindValue($key, $value);
            endif;
            $stm->execute();
            return (int) $con->lastInsertId();
        }
        catch(PDOException $ex)
        {
            throw new DatabaseError("No se pudo hacer el insert($consulta) con parametros ".json_encode($param)."porque : " . $ex->getMessage());
        }
    }

    /**
     * @extends 
     * 
     * @throws DataBaseError
     * 
     * @param string $consulta Consulta con el formato de la información a sobreescribir
     * @param array|null $param Parametros con la información a sobreescribir
     * @return void
     * @since 1.0
     */
    public static function update(string $consulta, array|null $param = null): void
    {
        $con = PDO_lib::openCon();
        try
        {
            $stm = $con->prepare($consulta);
            if(!$stm)
                throw new DatabaseError("No se pudo preparar el statement: " . $consulta);
            if($param != null):
                foreach($param as $key => $value)
                    if(is_int($key))
                        $stm->bindValue($key + 1, $value);
                    else
                        $stm->bindValue($key, $value);
            endif;
            $stm->execute();
        }
        catch(PDOException $ex)
        {
            throw new DatabaseError("Error en update $consulta \n " . json_encode($param). $ex->getMessage());
        }
    }

    /**
     * @extends 
     * 
     * @throws DataBaseError
     * 
     * @param string $consulta Consulta con el formato de la información a borrar
     * @param array|null $param Parametros con la información a borrar
     * @return void
     * @since 1.0
     */
    public static function delete(string $consulta, array|null $param = null): void
    {
        $con = PDO_lib::openCon();
        try
        {
            $stm = $con->prepare($consulta);
            if(!$stm)
                throw new DatabaseError("No se pudo preparar el statement: " . $consulta);
            if($param != null):
                foreach($param as $key => $value)
                    if(is_int($key))
                        $stm->bindValue($key + 1, $value);
                    else
                        $stm->bindValue($key, $value);
            endif;
            $stm->execute();
        } catch(PDOException $ex)
        {
            throw new DatabaseError("No se ha podido borrar porque: " . $ex->getMessage());
        }
    }

    /**
     * @extends
     * @throws DataBaseError
     * @return void
     * @since 1.0
     */
    public static function transaction(): void
    {
        $con = PDO_lib::openCon();
        try
        {
            if($con->inTransaction())
                throw new DatabaseError("Ya esta en una transacción");
            $con->beginTransaction();
        }
        catch(PDOException $ex)
        {
            throw new DatabaseError("No se ha podido iniciar una transaccion porque: " . $ex->getMessage());
        }
    }

    /**
     * @extends
     * @throws DataBaseError
     * @return void
     * @since 1.0
     */
    public static function commit(): void
    {
        $con = PDO_lib::openCon();
        try
        {
            if(!$con->inTransaction())
                throw new DatabaseError("No hay ninguna transacción en la que hacer commit");
            $con->commit();
        }
        catch(PDOException $ex)
        {
            throw new DatabaseError("No se ha podido hacer commit porque: " . $ex->getMessage());
        }
    }

    /**
     * @extends
     * @throws DataBaseError
     * @return void
     * @since 1.0
     */
    public static function rollback(): void
    {
        $con = PDO_lib::openCon();
        try
        {
            if(!$con->inTransaction())
                throw new DatabaseError("No hay ninguna transacción en la que hacer rollback");
            $con->rollBack();
        }
        catch(PDOException $ex)
        {
            throw new DatabaseError("No se ha podido hacer rollback porque: " . $ex->getMessage());
        }
    }
}