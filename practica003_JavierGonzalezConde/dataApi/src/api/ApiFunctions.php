<?php
namespace App\api;

trait ApiFunctions
{
    public function jsonSerialize(): array
    {
        return
        [
            "name"=>$this->getName(),
            "type"=>$this::tableName(),
            "values"=>$this->get_Values()
        ];
    }
}