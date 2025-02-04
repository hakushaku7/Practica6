<?php
namespace App\elements;

use App\api\ApiFunctions;
use config\DatabaseSelector;
use entidades\{Vehiculo, Marca};
use ExternalAccess\
    {
    api\server\ApiElement,
    database\template\Tabla,
    database\implementation\TablaPrototype,
    general\Filtrable
    };


class VehiculoDB extends Vehiculo implements Tabla, ApiElement, Filtrable
{
    /**
     * Establece el filtro pos kilometros maximos. Debe ser un int y se realiza con la siguiente correspondencia:
     *      0 (o menor)---> vehiculos con 0 km
     *      1          ---> vehiculos con 10 km o menos
     *      2          ---> vehiculos con 25 km o menos
     *      3          ---> vehiculos con 50 km o menos
     *      4          ---> vehiculos con 100 km o menos
     *      5          ---> vehiculos con 150 km o menos
     *      6          ---> vehiculos con cualquier cantidad de kilometros
     * @var string
     */
    public const FILTER_MAX_KM = "maxkm";
    
    /**
     * Filtra en función de la marca del vehiculo. Sera un int:
     *      menor que -1 ==> todos los vehiculos
     *      igual a   -1 ==> vehiculos con marca asignada
     *      igual a    0 ==> vehiculos sin marca asignada
     *      mayor que  0 ==> vehiculos de la marca cuyo id coincida con el valor indicado
     * 
     * @var string
     */
    public const FILTER_MARCA = "marca";

    /**
     * Filtra en función de si un vehiculo está reservado o no. Debe ser un int:
     *      menor que 0 ==> todos los vehiculos
     *      igual a   0 ==> vehiculos disponibles
     *      mayor que 0 ==> vehiculos reservados
     * @var string
     */
    public const FILTER_RESERVA = "reserva";

    protected int|null $marca;

    protected int $reservado;
    use TablaPrototype, DatabaseSelector, ApiFunctions;



    //_____________METIDODOS DE TABLA
    public static function tableName(): string
    {
        return "vehiculo";
    }

    public static function fields(): array
    {
        return [
            "modelo" => "modelo",
            "matricula" => "matricula",
            "kilometros" => "kilometros",
            "color" => "color",
            "marca" => "marca_id",
            "reservado" => "reservado"
        ];
    }

    public function get_Values(): array
    {
        return
            [
                "id" => $this->id,
                "modelo" => $this->modelo,
                "matricula" => $this->matricula,
                "kilometros" => $this->kilometros,
                "color" => $this->color,
                "marca_id" => $this->marca,
                "reservado" => $this->reservado
            ];
    }

    public function isReady(): bool
    {
        if(!isset($this->id)) return false;
        if(!isset($this->matricula)) return false;
        if(!isset($this->kilometros)) return false;
        if(!isset($this->color)) return false;
        return true;
    }

    //_____________________________METODOS de ELEMENTO
    public function getName(): string
    {
        return $this->modelo . " " . $this->matricula;
    }
 
     
 
    public static function placeholder(): self
    {
        $placeholder = new self();
        $placeholder->id = 0;
        $placeholder->modelo = "Modelo del vehiculo";
        $placeholder->matricula ="Matricula del vehiculo";
        $placeholder->kilometros = 0;
        $placeholder->color = "Color del vehiculo";
        $placeholder->marca = null;
        $placeholder->reservado = 0;
        return $placeholder;
    }

    public function setMarca(MarcaDB|int|null $marca):void
    {
        if($marca instanceof MarcaDB)
            $marca = $marca->getID();
        $this->marca = $marca;
    }

    //_______________________METODOS DE VEHICULO
    public function getMarca(): Marca|null
    {
        if($this->marca == null or $this->marca <= 0)
            return null;

        return MarcaDB::find($this->marca);
    }

    public function reservado(): bool
    {
        return self::database()::vehiculoReservado($this);
    }

    //________________METODOS de APIELEMENT

    public function load(array $data)
    {
        if(isset($data["modelo"])) $this->modelo = $data["modelo"];
        if(isset($data["color"])) $this->color = $data["color"];
        if(isset($data["kilometros"])) $this->kilometros = $data["kilometros"];
        if(isset($data["matricula"])) $this->matricula = $data["matricula"];
        if(isset($data["marca_id"])) $data["marca"] = $data["marca_id"];
        if(isset($data["marca"]))
        {
            $marca = $data["marca"];
            if(is_string($marca))
                if(is_numeric($marca))
                    $marca = (int) $marca;
                else
                    $marca = null;
            
            if(is_int($marca))
                $marca = ($marca <= 0) ? null : $marca;
            else
                $marca = null;
            
            $this->marca = $marca;
        }
    }

    //_______________________________METODOS FILTRABLE
    public static function getFiltered(array $filters): array
    {

        $result = [
            self::FILTER_MARCA => null,
            self::FILTER_MAX_KM => null,
            self::FILTER_RESERVA => null,
        ];

        if(isset($filters[self::FILTER_MAX_KM]))
        {
            $km = $filters[self::FILTER_MAX_KM];
            if(is_string($km))
                if(is_numeric($km))
                    $km = (int) $km;
            if(is_int($km))
                $result[self::FILTER_MAX_KM] = match(true)
                {
                    $km <= 0 => 0,
                    $km <= 1 => 10,
                    $km <= 2 => 25,
                    $km <= 3 => 50,
                    $km <= 4 => 100,
                    $km <= 5 => 150,
                    default => null
                };
        }
        
        if(isset($filters[self::FILTER_MARCA]))
        {
            $marca = $filters[self::FILTER_MARCA];
            if(is_string($marca))
                if(is_numeric($marca))
                    $marca = (int) $marca;
                
            if(is_int($marca))
                $result[self::FILTER_MARCA] = ($marca < -1) ? null: $marca;
        }

        if(isset($filters[self::FILTER_RESERVA]))
        {
            $reservado = $filters[self::FILTER_RESERVA];
            if(is_string($reservado))
                if(is_numeric($reservado))
                    $reservado = (int) $reservado;
            if(is_int($reservado))
                $result[self::FILTER_RESERVA] = ($reservado < 0) ? null: $reservado <=> 0;    
        }

        return self::database()::get_filtered(self::placeholder(), $result);

    }

    public static function get_filtered(array $filters): array
    {
        return self::getFiltered($filters);
    }
    //_________________________________METODOS PROPIOS
    public static function find_matricula(string $matricula): self
    {
        return self::database()::find_matricula($matricula);
    }


    
}