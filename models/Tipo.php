<?php

namespace Model;

class Tipo extends ActiveRecord
{
    protected static $tabla = 'tipos';
    protected static $columnasDB = ['id', "nombre"];

    public $id;
    public $nombre;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? "";
    }
}
