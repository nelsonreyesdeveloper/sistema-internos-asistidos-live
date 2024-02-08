<?php

namespace Model;

class User extends ActiveRecord
{
    protected static $tabla = 'users';
    protected static $columnasDB = ['id', 'name', 'dui'];

    public $id;
    public $name;
    public $dui;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->name = $args['nombre'] ?? '';
        $this->dui = $args['dui'] ?? null; 
    }

    public function validaruser()
    {

        if (!$this->name || trim($this->name) === '') {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        // if (!$this->dui) {
        //     self::$alertas['error'][] = 'El dui es obligatorio';
        // }
      
        if (strlen(trim($this->dui)) >= 1) {
            $formato = "/^\d{8}-\d{1}$/";
            if (!preg_match($formato, $this->dui)) {
                self::$alertas['error'][] = 'El formato del dui es incorrecto, debe ser: 00000000-0';
            }
        }
    }
}
