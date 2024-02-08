<?php

namespace Controllers;

use Model\Pena;
use Model\Tipo;
use Model\User;
use MVC\Router;

class UserController
{

    public function index(Router $router)
    {

        // Obtener los parámetros de la solicitud DataTables
        $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
        $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

        // Obtener datos desde la base de datos con filtrado y paginación
        $data = User::usersdatatablepaginate($start, $length, $searchValue);

        $records = User::obtenerTotalExpedientesusers($searchValue);

        // Estructurar la respuesta DataTables
        $response = array(
            "draw" => $draw,
            "recordsTotal" => $records,
            "recordsFiltered" => $records, // En este ejemplo, no hay filtrado adicional, por lo que es igual a recordsTotal
            "data" => $data["data"],
        );

        // Encabezado de tipo de contenido
        header('Content-Type: application/json');
        // Devolver datos JSON
        echo json_encode($response);
    }

    public function store()
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $user = new User($data);


        $user->validaruser();
        $errores = $user->getAlertas();
        $buscarrepetido = User::where('dui', $data['dui']);

        if (!empty($buscarrepetido) && strlen($buscarrepetido->dui) > 0) {

            $errores['error'][] = 'El dui ya existe';
        }

        if (empty($errores) && empty($buscarrepetido->dui)) {
            $user->guardar();
            $response = array(
                'success' => true
            );

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

    public function update()
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $user = User::find($data['id']);
        $user->name = $data['name'];
        $user->dui = $data['dui'];

        $user->validaruser();
        $errores = $user->getAlertas();
        $buscarrepetido = User::where('dui', $data['dui']);

        if (!empty($buscarrepetido) && $buscarrepetido->id != $user->id && strlen($buscarrepetido->dui) > 0) {

            $errores['error'][] = 'El dui ya existe';
        }


        if (empty($errores)) {
            $user->guardar();
            $response = array(
                'success' => true
            );

            echo json_encode($response);
        } else {
            $response = array(
                'success' => false,
                'errores' => $errores,
                'dueñodui' => $buscarrepetido
            );
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
}
