<?php
//załadowanie silnika szablonów Twig
require '../vendor/autoload.php';

//Handler do wyjątków i błędów
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

//uruchamiamy nową sesje - robimy to w programie TYLKO RAZ właśnie w tym miejscu
session_start();

//stworzenie obiektu klasy Router
$router = new Core\Router();

//korzystamy z METODY dodającej trasę do tablicy routingu
$router->add('',['controller'=>'Home','action'=>'index']);
$router->add('login',['controller'=>'Login','action'=>'new']);
$router->add('logout',['controller'=>'Login','action'=>'destroy']);

$router->add('{controller}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
$router->add('{controller}/{id:\d+}/{action}');

//trasa do resetowania hasła (token jest heksadecymalny)
$router->add('password/reset/{token:[\da-f]+}', ['controller'=>'Password', 'action'=>'reset']);

//trasa do aktywacji konta
$router->add('signup/activate/{token:[\da-f]+}', ['controller'=>'Signup', 'action'=>'activate']);

$router->add('balance',['controller'=>'Balance','action'=>'form']);

$router->dispatch($_SERVER['QUERY_STRING']);

?>