<?php

namespace Controllers;

use Model\Expediente;
use MVC\Router;

class ExpedienteController
{
    public function index()
    {



        // Obtener los parámetros de la solicitud DataTables
        $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
        $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
        $estado = isset($_GET['estado']) ? $_GET['estado'] : '';


        $data = Expediente::expedientesdatatablepaginate($start, $length, $searchValue, $estado);

        // Estructurar la respuesta DataTables
        $response = array(
            "draw" => $draw,
            "recordsTotal" => $data["recordsTotal"],
            "recordsFiltered" => $data["recordsFiltered"], // En este ejemplo, no hay filtrado adicional, por lo que es igual a recordsTotal
            "data" => $data["data"],
        );

        // Encabezado de tipo de contenido
        header('Content-Type: application/json');
        // Devolver datos JSON
        echo json_encode($response);
    }

    public function destroy()
    {
        $id = $_GET['id'];
        if (is_numeric($id)) {
            $expediente = Expediente::find($id);
            $delete =  $expediente->eliminar();
            echo json_encode(array('success' => 'Expediente eliminado correctamente'));
        } else {
            echo json_encode(array('error' => 'No se ha podido eliminar el expediente'));
        }
    }

    public function create()
    {
        // Recuperar datos del cuerpo de la solicitud POST
        $jsonData = file_get_contents('php://input');
        // Decodificar el JSON en un array asociativo
        $data = json_decode($jsonData, true);


        $expediente = new Expediente();
        $expediente->tipo_id = $data['select-tipo'];
        $expediente->n_expediente = $data['n-expediente'];
        $expediente->user_id = $data['id-interno'];
        $expediente->delito = $data['input-delito'];
        $expediente->fecha_sentencia = $data['fecha-sentencia'];
        $expediente->pena = $data['input-pena'];
        $expediente->fecha_ingreso = $data['fecha-ingreso'];
        $expediente->observaciones = $data['observaciones'];
        $expediente->pena_id = $data['select-pena'];

        $expediente->validarExpediente();
        $errores = $expediente->getAlertas();

        if (empty($errores)) {

            $expediente->guardar();

            $response = array(
                'success' => true,
            );
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            $response = array(
                'success' => false,
                'errores' => $errores
            );
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }

    public function status()
    {
        $id = $_GET['id'];
        // Recuperar datos del cuerpo de la solicitud POST
        $jsonData = file_get_contents('php://input');
        // Decodificar el JSON en un array asociativo
        $data = json_decode($jsonData, true);
        $estadonuevo = $data['estadoactual'] == 0 ? 1 : 0;
        $expediente = Expediente::find($id);
        $expediente->archivado = $estadonuevo;

        if ($expediente->archivado == 1) {
            $expediente->updated_at = date('Y-m-d');
        } else {
            $expediente->updated_at = null;
        }

        $resultado =  $expediente->guardar();

        echo json_encode(array('success' => $expediente->archivado == 1 ? "archivado" : "desarchivado"));
    }

    public function update()
    {
        $id = $_GET['id'];

        $jsonData = file_get_contents('php://input');

        $data = json_decode($jsonData, true);
        $expediente = Expediente::find($id);
        $expediente->user_id = $data['id-interno'];
        $expediente->tipo_id = $data['select-tipo-edit'];
        $expediente->n_expediente = $data['n-expediente-edit'];
        $expediente->delito = $data['input-delito-edit'];
        $expediente->fecha_sentencia = $data['fecha-sentencia-edit'];
        $expediente->pena = $data['input-pena-edit'];
        $expediente->fecha_ingreso = $data['fecha-ingreso-edit'];
        $expediente->pena_id = $data['select-pena-accesoria-edit'];
        $expediente->observaciones = $data['observaciones-edit'];

        $expediente->validarExpediente();
        $errores = $expediente->getAlertas();


        if (empty($errores)) {
            $expediente->guardar();
            $response = array(
                'success' => true,
            );
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {

            $response = array(
                'success' => false,
                'errores' => $errores
            );
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
}
