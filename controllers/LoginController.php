<?php

namespace Controllers;

use Classes\Email;
use Model\Admin;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {

        session_start();

        if (isset($_SESSION['login'])) {
            header('Location: /dashboard');
        }
        
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Admin($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Admin::where('email', $auth->email);

                if ($usuario) {
                    // Verificar el password
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar el usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;


                        // Redireccionamiento
                        if ($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /dashboard');
                        } else {
                            header('Location: /');
                        }
                    }
                } else {
                    Admin::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Admin::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas,
            'titulo' => 'Sistema Internos/Asistidos - Login'
        ]);
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }
}
