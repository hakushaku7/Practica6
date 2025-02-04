<?php
namespace App\elements;

require_once __DIR__ . "/../../vendor/autoload.php";
//require_once __DIR__ . "/../../bendor/manuaload.php";

use config\DatabaseSelector;
use App\api\ApiFunctions;
use entidades\{Reserva, Vehiculo};
use ExternalAccess\exceptions\{ElementNotFound, FormatError, UncompletedCode};
use ExternalAccess\
    {
        api\server\ApiElement,
        database\template\Tabla,
        database\implementation\TablaPrototype
    };


class ReservaDB extends Reserva implements Tabla, ApiElement
{
    use TablaPrototype , DatabaseSelector, ApiFunctions;

    protected int|null $vehiculo;

    //___________________METODOS DE TABLA
    public static function tableName(): string
    {
        return "reserva";
    }

    public static function fields(): array
    {
        return [
            "vehiculo" => "vehiculo_id",
            "nombre" => "nombre",
            "apellidos" => "apellidos",
            "dni" => "dni"
        ];
    }

    public function get_Values(): array
    {
        return
            [
                "id" => $this->id,
                "vehiculo_id" => $this->vehiculo,
                "nombre" => $this->nombre,
                "apellidos" => $this->apellidos,
                "dni" => $this->dni
            ];
    }



//________METODOS ELEMENTO

    public function getName(): string
    {
        return $this->getNombre() . " " . $this->getApellidos();
    }

    public static function placeholder(): self
    {
        $var = new ReservaDB();
        $var->id = 0;
        $var->nombre = "Nombre reserva";
        $var->apellidos = "Apellidos reserva";
        $var->dni = "DNI";
        $var->vehiculo = null;
        return $var;
    }
    

    public function isReady(): bool
    {
        if(!isset($this->id)) return false;
        if(!isset($this->nombre)) return false;
        if(!isset($this->apellidos)) return false;
        if(!isset($this->dni)) return false;
        
        return true;

    }



    //________METODOS RESERVA

    public function setVehiculo(Vehiculo|null $vehiculo): void
    {
        if($vehiculo == null) $this->vehiculo = null;
        else if(!$vehiculo instanceof VehiculoDB) throw new FormatError("El vehiculo añadido no es compatible con la base de datos");
        else if(!VehiculoDB::check_ID($vehiculo->getID())) throw new ElementNotFound("El vehiculo añadido no existe en la base de datos");
        else $this->vehiculo = $vehiculo->getID();
    }

    public function getVehiculo(): Vehiculo|null
    {
        if($this->vehiculo == null) return null;
        return VehiculoDB::find($this->vehiculo);
    }

    public function getVehiculoID(): int|null
    {
        return $this->vehiculo;
    }

    //_________________________METODOS DE APIELEMENT
    public function load(array $data)
    {
        if(isset($data["id"])) $this->id = $data["id"];
        if(isset($data["nombre"])) $this->nombre = $data["nombre"];
        if(isset($data["apellidos"])) $this->apellidos = $data["apellidos"];
        if(isset($data["dni"])) $this->dni = $data["dni"];
        if(isset($data["vehiculo_id"])) $data["vehiculo"] = $data["vehiculo_id"];
        if(isset($data["vehiculo"]))
        {
            $vehiculo = $data["vehiculo"];
            if(is_string($vehiculo))
                if(is_numeric($vehiculo))
                    $vehiculo = (int) $vehiculo;
                else
                    $vehiculo = null;

            if(is_int($vehiculo))
                $vehiculo = ($vehiculo <= 0) ? null : $vehiculo;
            else
                $vehiculo = null;
            
            $this->vehiculo = $vehiculo;
        }

    }

}