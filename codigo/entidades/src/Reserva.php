<?php
namespace entidades;

abstract class Reserva
{

    protected int $id;
    protected string $nombre;
    protected string $apellidos;
    protected string $dni;
    public function getID(): int
    {
        return $this->id;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setApellidos(string $apellidos): void
    {
        $this->apellidos = $apellidos;
    }
    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    public function setDNI(string $dni): void
    {
        $this->dni = $dni;
    }
    public function getDNI(): string
    {
        return $this->dni;
    }

    public abstract function getVehiculo(): Vehiculo|null;

   //public abstract function setVehiculo(Vehiculo|null $vehiculo): void;
   


}