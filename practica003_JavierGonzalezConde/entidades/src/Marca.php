<?php
namespace entidades;

abstract class Marca
{
    protected int $id;
    protected string  $nombre;

    public function getID(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
}