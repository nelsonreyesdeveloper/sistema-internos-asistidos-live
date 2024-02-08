<?php

namespace Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Model\Expediente;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportarController
{


    public function pdf()
    {
        // Recuperar datos del cuerpo de la solicitud POST
        $jsonData = file_get_contents('php://input');

        $estado = isset($_GET['estado']) ? $_GET['estado'] : "";

        // Decodificar el JSON en un array asociativo
        $data = json_decode($jsonData, true);

        // Acceder a los datos específicos
        $searchValue = $data['searchData'];

        $expedientes = [];

        if (!empty($searchValue)) {
            $expedientes = Expediente::search($searchValue, $estado);
        } else {
            $expedientes = Expediente::allexpedientes($estado);
        }

        if (empty($expedientes)) {
            $response = array("error" => "No hay resultados");
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }


        $data = array(
            'expedientes' => $expedientes,
        );


        echo json_encode($data, JSON_FORCE_OBJECT);
    }
}
