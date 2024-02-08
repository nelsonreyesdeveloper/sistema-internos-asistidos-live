<?php

namespace Model;

class Expediente extends ActiveRecord
{
    protected static $tabla = 'expedientes';
    protected static $columnasDB = ['id', 'tipo_id', 'n_expediente', 'user_id', 'delito', 'fecha_sentencia', 'pena', 'fecha_ingreso', 'observaciones', 'archivado', 'pena_id','updated_at'];

    public $id;
    public $tipo_id;
    public $n_expediente;
    public $user_id;

    public $delito;
    public $fecha_sentencia;
    public $pena;
    public $fecha_ingreso;
    public $observaciones;
    public $archivado;

    public $pena_id;

    public $updated_at;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->tipo_id = $args['tipo_id'] ?? '';
        $this->n_expediente = $args['n_expediente'] ?? '';
        $this->user_id = $args['user_id'] ?? '';
        $this->delito = $args['delito'] ?? '';
        $this->fecha_sentencia = $args['fecha_sentencia'] ?? '';
        $this->pena = $args['pena'] ?? '';
        $this->fecha_ingreso = $args['fecha_ingreso'] ?? '';
        $this->observaciones = $args['observaciones'] ?? '';
        $this->archivado = $args['archivado'] ?? 0;
        $this->pena_id = $args['pena_id'] ?? '';
        $this->updated_at = $args['updated_at'] ?? null;
    }


    public function validarExpediente()
    {

        if (!$this->n_expediente || trim($this->n_expediente) === '') {
            self::$alertas['error'][] = 'El numero de expediente es obligatorio';
        }
        if (!$this->fecha_sentencia) {
            self::$alertas['error'][] = 'La fecha de sentencia es obligatoria';
        }

        if (!$this->fecha_ingreso) {
            self::$alertas['error'][] = 'La fecha de ingreso es obligatoria';
        }

        if (!$this->pena || trim($this->pena) === '') {
            self::$alertas['error'][] = 'La pena es obligatoria';
        }

        if (!$this->user_id || !is_numeric($this->user_id)) {
            self::$alertas['error'][] = 'El usuario es obligatorio';
        }

        if (!$this->delito || trim($this->delito) === '') {
            self::$alertas['error'][] = 'El delito es obligatorio';
        }
        if (!$this->pena_id || !is_numeric($this->pena_id)) {
            self::$alertas['error'][] = 'La pena es obligatoria';
        }

        if (!$this->tipo_id || !is_numeric($this->tipo_id)) {
            self::$alertas['error'][] = 'El tipo de expediente es obligatorio';
        }
    }
}
