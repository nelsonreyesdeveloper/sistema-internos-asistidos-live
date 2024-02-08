<?php

namespace Controllers;

use Model\Pena;
use Model\Tipo;
use Model\User;
use MVC\Router;

class IndexController
{

    public function index(Router $router)
    {

        session_start();
        /* Ver si el usuario tiene permisos */
        isAuth();

        $tipos = Tipo::all();
        $penas = Pena::all();

        return $router->render('expedientes/index', [
            'titulo' => 'SISTEMA INTERNOS/ASISTIDOS',
            'tipos' => $tipos,
            'penas' => $penas
        ]);
    }

    public function busqueda()
    {
        $input = $_GET['search'];
        $users = User::whereLike('name', $input);

        echo json_encode($users);
    }
}
