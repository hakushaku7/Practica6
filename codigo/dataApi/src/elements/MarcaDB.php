<?php
namespace App\elements;


use entidades\Marca;
use config\DatabaseSelector;
use App\api\ApiFunctions;
use ExternalAccess\
    {
    database\template\Tabla,
    database\implementation\TablaPrototype,
    api\server\ApiElement
    };

class MarcaDB extends Marca implements Tabla, ApiElement
{
    use TablaPrototype, DatabaseSelector, ApiFunctions;
    



    //_____________METIDODOS DE TABLA
    public static function tableName(): string
    {
        return "marca";
    }

    public static function fields(): array
    {
        return [
            "nombre" => "nombre"
        ];
    }

    public function get_Values(): array
    {
        return
            [
                "id" => $this->id,
                
                "nombre" => $this->nombre
            ];
    }

    public function isReady(): bool
    {
        if(!isset($this->id)) return false;
        if(!isset($this->nombre)) return false;
        return true;
    }
    //_____________________________METODOS de ELEMENTO
    public function getName(): string
    {
        return $this->nombre;
    }

    

    public static function placeholder(): self
    {
        $placeholder = new self();
        $placeholder->id = 0;
        $placeholder->nombre = "Nombre de marca";
        return $placeholder;
    }


    //________METODOS ApiElement

    public function load(array $data)
    {
        if(isset($data["nombre"])) $this->setNombre($data["nombre"]);
    }

   
    
}