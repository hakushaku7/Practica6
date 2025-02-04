<?php
namespace entidades;

abstract class Vehiculo
{
    protected int $id;

    protected string $modelo;
    protected string $matricula;
    protected int $kilometros;
    protected string $color;

    
    public function getID(): int
    {
        return $this->id;
    }

    public function setModelo(string $modelo): void
    {
        $this->modelo = $modelo;

    }

    public function getModelo(): string
    {
        return $this->modelo;
    }

    public function setMatricula(string $matricula): void
    {
        $this->matricula = $matricula;
    }

    public function getMatricula(): string
    {
        return $this->matricula;
    }

    public function setKilometros(int $kilometros): void
    {
        $this->kilometros = $kilometros;
    }

    public function getKilometros(): int
    {
        return $this->kilometros;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public abstract function getMarca(): Marca|null;


    public abstract function reservado(): bool;



}