<?php

namespace MVC;

class Router
{
    public  $getRoutes = [];
    public  $postRoutes = [];

    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {

        $url_actual = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $fn = $this->getRoutes[$url_actual] ?? null;
        } else {
            $fn = $this->postRoutes[$url_actual] ?? null;
        }

        // Debugging output

        if ($fn && is_array($fn) && count($fn) === 2) {
            // Extract controller and method names
            list($controllerName, $methodName) = $fn;

            // Check if the class exists
            if (class_exists($controllerName)) {
                // Create an instance of the controller
                $controller = new $controllerName();

                // Check if the method exists and is callable
                if (method_exists($controller, $methodName) && is_callable([$controller, $methodName])) {
                    // Call the method on the instance
                    $controller->$methodName($this);
                } else {
                    echo "Método no encontrado o no es accesible en el controlador";
                }
            } else {
                echo "Clase de controlador no encontrada";
            }
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }


    public function render($view, $datos = [])
    {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        ob_start();

        include_once __DIR__ . "/views/$view.php";

        $contenido = ob_get_clean(); // Limpia el Buffer

        include_once __DIR__ . '/views/layout.php';
    }
}
