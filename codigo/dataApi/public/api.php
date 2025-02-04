<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;


require_once __DIR__ . "/../config/preload.php";

$app = AppFactory::create();


$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->setBasePath("/practica003_JavierGonzalezConde/dataApi/public/api");


$app->get("/", function(Request $request, Response $response){
    $response->getBody()->write("conectado");
    return $response;
});

include __DIR__ . "/ApiMarca.php";
include __DIR__ . "/ApiVehiculo.php";
include __DIR__ . "/ApiReserva.php";
include __DIR__ . "/ApiFunciones.php";
$app->run();