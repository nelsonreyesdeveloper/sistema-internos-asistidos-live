<?php

namespace Model;

class ActiveRecord
{

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];

    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database)
    {
        self::$db = $database;
    }

    // Setear un tipo de Alerta
    public static function setAlerta($tipo, $mensaje)
    {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Obtener las alertas
    public static function getAlertas()
    {
        return static::$alertas;
    }

    // Validación que se hereda en modelos
    public function validar()
    {
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria (Active Record)
    public static function consultarSQL($query)
    {
        // Consultar la base de datos
        $resultado = self::$db->query($query);


        // Iterar los resultados
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro)
    {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos()
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public static function obtenerTotalExpedientes($searchValue, $estado)
    {

        $whereClauseEstado = "";  // Inicializa la variable para que esté vacía

        if ($estado == 'fd4d6e6b5623d265f5b3b3422aae62b28215d55fc6c67e2424f7d98b16df5b08') {
            $whereClauseEstado = " (expedientes.archivado = '1')";
        }
        if ($estado == 'c135f01ff8c8edf1b34a285e97a98b0e5019e6c5932ecb0d575e56f8a2f2b541') {
            $whereClauseEstado = " (expedientes.archivado = '0')";
        }


        $query = "";
        if (preg_match('/^"([^"]*)"$/u', $searchValue, $matches)) {
            // Si está entre comillas, obtener el texto dentro de las comillas
            if (strlen($matches[1]) > 0) {
                $query = "SELECT COUNT(*) as total
            FROM expedientes
            LEFT JOIN users ON expedientes.user_id = users.id
            LEFT JOIN penas ON expedientes.pena_id = penas.id
            LEFT JOIN tipos ON expedientes.tipo_id = tipos.id
            WHERE tipos.nombre LIKE '%$matches[1]'";
            } else {
                $query = "SELECT COUNT(*) as total
                FROM expedientes
                LEFT JOIN users ON expedientes.user_id = users.id
                LEFT JOIN penas ON expedientes.pena_id = penas.id
                LEFT JOIN tipos ON expedientes.tipo_id = tipos.id
                WHERE  (expedientes.n_expediente LIKE '%$searchValue%'
                OR users.name LIKE '%$searchValue%'
                OR expedientes.delito LIKE '%$searchValue%'
                OR expedientes.fecha_ingreso LIKE '%$searchValue%'
                OR expedientes.observaciones LIKE '%$searchValue%'
                OR tipos.nombre LIKE '%$searchValue%')";
            }
        } else {
            $query = "SELECT COUNT(*) as total
                FROM expedientes
                LEFT JOIN users ON expedientes.user_id = users.id
                LEFT JOIN penas ON expedientes.pena_id = penas.id
                LEFT JOIN tipos ON expedientes.tipo_id = tipos.id
                WHERE  (expedientes.n_expediente LIKE '%$searchValue%'
                OR users.name LIKE '%$searchValue%'
                OR expedientes.delito LIKE '%$searchValue%'
                OR expedientes.fecha_ingreso LIKE '%$searchValue%'
                OR expedientes.observaciones LIKE '%$searchValue%'
                OR tipos.nombre LIKE '%$searchValue%')";
        }


        if (!empty($whereClauseEstado)) {
            $query .= "AND " . $whereClauseEstado;
        }

        // Ejecutar la consulta
        $resultado = self::$db->query($query);

        // Verificar errores en la ejecución de la consulta
        if (!$resultado) {
            die("Error en la ejecución de la consulta: " . self::$db->error);
        }

        // Obtener el resultado
        $result = $resultado->fetch_assoc();

        // Retornar el total de registros filtrados
        return $result['total'] ?? 0;
    }
    // En la clase Expediente

    public static function allexpedientes($estado)
    {

        if ($estado == 'fd4d6e6b5623d265f5b3b3422aae62b28215d55fc6c67e2424f7d98b16df5b08') {
            $whereClauseEstado = " (expedientes.archivado = '1')";
        }
        if ($estado == 'c135f01ff8c8edf1b34a285e97a98b0e5019e6c5932ecb0d575e56f8a2f2b541') {
            $whereClauseEstado = " (expedientes.archivado = '0')";
        }

        $sql = "SELECT
           expedientes.id,
                tipos.nombre AS Tipo,
                expedientes.n_expediente AS 'N° Expediente',
                users.name AS 'Nombre Interno/Asistido',
                expedientes.delito as 'Delito',
                expedientes.fecha_sentencia as 'Fecha Sentencia',
                expedientes.pena as 'Pena',
                expedientes.fecha_ingreso as 'Fecha Ingreso',
                expedientes.observaciones as 'Observaciones',
                penas.nombre AS 'Pena Accesoria',
                expedientes.archivado as 'Estado',
                expedientes.updated_at as 'Fecha Archivado'
            FROM expedientes
            LEFT JOIN users ON expedientes.user_id = users.id
            LEFT JOIN penas ON expedientes.pena_id = penas.id
            LEFT JOIN tipos ON expedientes.tipo_id = tipos.id";

        // Agregar la condición del estado si existe
        if (!empty($whereClauseEstado)) {
            $sql .= " WHERE" . $whereClauseEstado;
        }

        $sql .= " ORDER BY expedientes.id ASC";


        $resultado = self::$db->query($sql);

        // Verificar errores en la ejecución de la consulta
        if (!$resultado) {
            die("Error en la ejecución de la consulta: " . self::$db->error);
        }

        // Iterar los resultados para mostrar
        $arrayMostrar = [];
        while ($registro = $resultado->fetch_assoc()) {
            $arrayMostrar[] = $registro;
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados
        return $arrayMostrar;
    }

    public static function search($searchValue, $estado)
    {
        if ($estado == 'fd4d6e6b5623d265f5b3b3422aae62b28215d55fc6c67e2424f7d98b16df5b08') {
            $whereClauseEstado = " (expedientes.archivado = '1')";
        }
        if ($estado == 'c135f01ff8c8edf1b34a285e97a98b0e5019e6c5932ecb0d575e56f8a2f2b541') {
            $whereClauseEstado = " (expedientes.archivado = '0')";
        }

        $whereClause = "";
        if (preg_match('/^"([^"]*)"$/u', $searchValue, $matches)) {
            // Si está entre comillas, obtener el texto dentro de las comillas
            if (strlen($matches[1]) > 0) {
                $whereClause = "AND (tipos.nombre LIKE '%$matches[1]')";
            } else {
                $whereClause = "AND (
                    tipos.nombre LIKE '%$searchValue'
                   OR users.name LIKE '%$searchValue%'
                   OR expedientes.n_expediente LIKE '%$searchValue%'
                   OR expedientes.delito LIKE '%$searchValue%'
                   OR expedientes.fecha_ingreso LIKE '%$searchValue%'
                   OR expedientes.observaciones LIKE '%$searchValue%'
               )";
            }
        } else {
            $whereClause = "AND (
                tipos.nombre LIKE '%$searchValue'
               OR users.name LIKE '%$searchValue%'
               OR expedientes.n_expediente LIKE '%$searchValue%'
               OR expedientes.delito LIKE '%$searchValue%'
               OR expedientes.fecha_ingreso LIKE '%$searchValue%'
               OR expedientes.observaciones LIKE '%$searchValue%'
           )";
        }

        if (!empty($whereClauseEstado)) {
            $whereClause .= " AND $whereClauseEstado";
        }

        // Modificar la ordenación según el caso
        $orderBy = ($searchValue == '') ? 'ORDER BY id ASC' : 'ORDER BY expedientes.fecha_ingreso ASC';

        // Modificar la consulta principal para obtener solo los registros de la página actual
        $sql = "SELECT
            expedientes.id,
            tipos.nombre AS Tipo,
            expedientes.n_expediente AS 'N° Expediente',
            users.name AS 'Nombre Interno/Asistido',
            expedientes.delito as 'Delito',
            expedientes.fecha_sentencia as 'Fecha Sentencia',
            expedientes.pena as 'Pena',
            expedientes.fecha_ingreso as 'Fecha Ingreso',
            expedientes.observaciones as 'Observaciones',
            penas.nombre AS 'Pena Accesoria',
            expedientes.archivado as 'Estado',
            expedientes.updated_at as 'Fecha Archivado'
        FROM expedientes
        LEFT JOIN users ON expedientes.user_id = users.id
        LEFT JOIN penas ON expedientes.pena_id = penas.id
        LEFT JOIN tipos ON expedientes.tipo_id = tipos.id
        WHERE 1 $whereClause
        $orderBy";

        $resultado = self::$db->query($sql);

        // Verificar errores en la ejecución de la consulta
        if (!$resultado) {
            die("Error en la ejecución de la consulta: " . self::$db->error);
        }

        // Iterar los resultados para mostrar
        $arrayMostrar = [];
        while ($registro = $resultado->fetch_assoc()) {
            $arrayMostrar[] = $registro;
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados
        return $arrayMostrar;
    }
    public static function expedientesdatatablepaginate($start, $length, $searchValue, $estado)
    {
        $whereClauseEstado = "";  // Inicializa la variable para que esté vacía

        if ($estado == 'fd4d6e6b5623d265f5b3b3422aae62b28215d55fc6c67e2424f7d98b16df5b08') {
            $whereClauseEstado = " (expedientes.archivado = '1')";
        }
        if ($estado == 'c135f01ff8c8edf1b34a285e97a98b0e5019e6c5932ecb0d575e56f8a2f2b541') {
            $whereClauseEstado = " (expedientes.archivado = '0')";
        }


        $totalRecords = self::obtenerTotalExpedientes($searchValue, $estado);
        $whereClause = "";
        if (preg_match('/^"([^"]*)"$/u', $searchValue, $matches)) {
            // Si está entre comillas, obtener el texto dentro de las comillas
            if (strlen($matches[1]) > 0) {
                $whereClause = "AND (tipos.nombre LIKE '%$matches[1]')";
            } else {
                $whereClause = "AND (
                    tipos.nombre LIKE '%$searchValue'
                   OR users.name LIKE '%$searchValue%'
                   OR expedientes.n_expediente LIKE '%$searchValue%'
                   OR expedientes.delito LIKE '%$searchValue%'
                   OR expedientes.fecha_ingreso LIKE '%$searchValue%'
                   OR expedientes.observaciones LIKE '%$searchValue%'
               )";
            }
        } else {
            $whereClause = "AND (
                tipos.nombre LIKE '%$searchValue'
               OR users.name LIKE '%$searchValue%'
               OR expedientes.n_expediente LIKE '%$searchValue%'
               OR expedientes.delito LIKE '%$searchValue%'
               OR expedientes.fecha_ingreso LIKE '%$searchValue%'
               OR expedientes.observaciones LIKE '%$searchValue%'
                

           )";
        }
        if (!empty($whereClauseEstado)) {
            $whereClause .= " AND " . $whereClauseEstado;
        }


        // Modificar la ordenación según el caso
        $orderBy = ($searchValue == '') ? 'ORDER BY id ASC' : 'ORDER BY expedientes.fecha_ingreso ASC';

        // Modificar la consulta principal para obtener solo los registros de la página actual
        $sql = "SELECT
        expedientes.fecha_ingreso,
        expedientes.fecha_sentencia,
        expedientes.delito,
        expedientes.id,
        expedientes.archivado,
        expedientes.observaciones,
        expedientes.pena,
        expedientes.n_expediente,
        expedientes.user_id,
        expedientes.pena_id,
        expedientes.tipo_id,
        expedientes.updated_at,
        users.name AS nombre_usuario,
        penas.nombre AS tipo_pena,
        tipos.nombre AS tipo_expediente
    FROM expedientes
    LEFT JOIN users ON expedientes.user_id = users.id
    LEFT JOIN penas ON expedientes.pena_id = penas.id
    LEFT JOIN tipos ON expedientes.tipo_id = tipos.id
    WHERE 1 $whereClause
    $orderBy
    LIMIT $start, $length";


        $resultado = self::$db->query($sql);

        // Verificar errores en la ejecución de la consulta
        if (!$resultado) {
            die("Error en la ejecución de la consulta: " . self::$db->error);
        }

        // Iterar los resultados para mostrar
        $arrayMostrar = [];
        while ($registro = $resultado->fetch_assoc()) {
            $arrayMostrar[] = $registro;
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados y el total de registros sin paginación
        return array('data' => $arrayMostrar, 'recordsTotal' => $totalRecords, 'recordsFiltered' => $totalRecords);
    }

    public static function obtenerTotalExpedientesusers($searchValue)
    {
        $query = "SELECT COUNT(*) as total
        FROM users
        WHERE name LIKE '%$searchValue%'
        OR dui LIKE '%$searchValue%'";


        // Ejecutar la consulta
        $resultado = self::$db->query($query);

        // Verificar errores en la ejecución de la consulta
        if (!$resultado) {
            die("Error en la ejecución de la consulta: " . self::$db->error);
        }

        // Obtener el resultado
        $result = $resultado->fetch_assoc();

        // Retornar el total de registros filtrados
        return $result['total'] ?? 0;
    }

    public static function usersdatatablepaginate($start, $length, $searchValue)
    {
        $totalRecords = self::obtenerTotalExpedientesusers($searchValue);

        if (trim($searchValue) == '') {
            $whereClause = '';
        } else {
            $whereClause = "AND (
                name LIKE '%$searchValue%'
                OR dui LIKE '%$searchValue%'
            )";
        }

        // Modificar la ordenación según el caso
        $orderBy = 'ORDER BY id DESC';

        // Modificar la consulta principal para obtener solo los registros de la página actual
        $sql = "SELECT *
                FROM users
                WHERE 1 $whereClause
                $orderBy
                LIMIT $start, $length";

        $resultado = self::$db->query($sql);

        // Verificar errores en la ejecución de la consulta
        if (!$resultado) {
            die("Error en la ejecución de la consulta: " . self::$db->error);
        }

        // Iterar los resultados para mostrar
        $arrayMostrar = [];
        while ($registro = $resultado->fetch_assoc()) {
            $arrayMostrar[] = $registro;
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados y el total de registros sin paginación
        return array('data' => $arrayMostrar, 'recordsTotal' => $totalRecords, 'recordsFiltered' => $totalRecords);
    }


    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos()
    {
        $atributos = $this->atributos();



        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            if ($value === null) {
                $sanitizado[$key] = null;
            } else {
                $sanitizado[$key] = self::$db->escape_string($value);
            }
        }

        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    // Registros - CRUD
    public function guardar()
    {
        $resultado = '';
        if (!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    // Obtener todos los Registros
    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id)
    {
        $query = "SELECT * FROM " . static::$tabla  . " WHERE id = ${id}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite)
    {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT ${limite} ORDER BY id DESC";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Busqueda Where con Columna 
    public static function where($columna, $valor)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ${columna} = '${valor}'";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public static function whereLike($columna, $valor)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ${columna} LIKE '%${valor}%'";
        $resultados = self::consultarSQL($query);
        return $resultados;
    }


    // crea un nuevo registro
    public function crear()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();
        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (";


        $values = [];
        foreach ($atributos as $value) {
            // Manejar valores nulos
            if ($value === null) {
                $values[] = "NULL";
            } else {
                $values[] = "'" . self::$db->escape_string($value) . "'";
            }
        }

        $query .= join(', ', $values);
        $query .= " ) ";

        // debuguear($query); // Descomentar si no te funciona algo

        // Resultado de la consulta
        $resultado = self::$db->query($query);
        return [
            'resultado' =>  $resultado,
            'id' => self::$db->insert_id
        ];
    }

    // Actualizar el registro
    public function actualizar()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();


        $valores = [];
        foreach ($atributos as $key => $value) {
            // Manejar valores nulos
            if ($value === null) {
                $valores[] = "{$key} = NULL";
            } else {
                $valores[] = "{$key}='{$value}'";
            }
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla . " SET ";
        $query .=  join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Eliminar un Registro por su ID
    public function eliminar()
    {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }
}
