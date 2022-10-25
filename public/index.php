<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use RedBeanPHP\R as R;

require_once '../vendor/autoload.php';
R::setup('mysql:host=localhost;dbname=UR_DB_NAME', 'root', '');

$loader = new FilesystemLoader('../views');
$twig = new Environment($loader);

if (isset($_GET['controller'])) {
    $params = explode("/", $_GET['controller']);
}

// controller checken -> default
if (isset($_GET['controller'])) {
    $controllername = ucfirst($params[0]) . "Controller";
    if (!class_exists($controllername)) {
        error(404, 'Controller not found');
        exit;
    }
} else {
    $controllername = "HomeController";
}

// method checken -> default
if (isset($params[1])) {
    $method = $params[1];
    if (!method_exists($controllername, $method)) {
        error(404, 'Method not found');
        exit;
    }
} else {
    $method = 'index';
}

//pagina bouwladen
$controller = new $controllername();
echo $controller->$method();