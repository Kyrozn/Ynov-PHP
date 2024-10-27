<?php
class Router
{
    public $routes = [
        '/' => './src/index.php',
        '/login' => './src/Log/login.php',
        '/logout' => './src/Log/logout.php',
        '/register' => './src/Log/register.php',
        '/profil' => './src/User/profil.php',
        '/project' => './src/User/project.php',
        '/admin' => './src/User/adminPanel.php',
    ];
    // Exécuter le routeur
    public function dispatch($requestedUri)
    {
        // Extraire l'URI sans les paramètres GET
        $requestedUri = parse_url($requestedUri, PHP_URL_PATH);
        
        // Vérifier si la route existe
        if (array_key_exists($requestedUri, $this->routes)) {
            $file = $this->routes[$requestedUri];
            if (is_callable($file)) {
                $_ = call_user_func($file);
            } else {
                include $file;
            }
        } else {
            // Si aucune route ne correspond, afficher une erreur 404
            $this->defaultRoute();
        }
    }

    // Route par défaut pour 404
    private function defaultRoute()
    {
        http_response_code(404);
        echo "404 Not Found";
    }
}
