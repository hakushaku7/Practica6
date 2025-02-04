<?php

use App\elements\MarcaDB;
use ExternalAccess\exceptions\{ElementNotFound, ExternalError, InconsistencyError};
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
$path = "/marca";

$app->get($path, function(Request $request, Response $response)
{
    $respuesta = [];
    try
    {
        $respuesta["result"] = MarcaDB::get();
        $respuesta["ok"] = True;
    }
    catch(ExternalError $ex)
    {
        $respuesta["error"] = "Error con la fuente externa\n" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    
    $response->getBody()->write(json_encode($respuesta));
    return $response->withHeader("Content-Type","application/json");
    
});
$app->get($path . "/{id}", function(Request $request, Response $response, array $args){
    $id = $args["id"];
    $respuesta = [];
    try
    {

        $respuesta["result"] = MarcaDB::find($id);
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(ExternalError $ex)
    {
        $respuesta["error"] = "Error con la fuente externa\n" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
        
    
    $response->getBody()->write(json_encode($respuesta));
    return $response->withHeader("Content-Type","application/json");
});


$app->post($path, function(Request $request, Response $response, array $args){
    $data =  $request->getParsedBody();
    $var = MarcaDB::placeholder();
    $respuesta = [];
    $var->load($data);
    try
    {
        $var->save();
        $respuesta["result"] = MarcaDB::find($var->getID());
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(ExternalError $ex)
    {
        $respuesta["error"] = "Error con la fuente externa\n" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT));
    return $response->withHeader("Content-Type","application/json");
});


$app->patch($path."/{id}", function(Request $request, Response $response, array $args){
    $id = $args["id"];
    $data =  $request->getParsedBody();
    
    $respuesta = [];
    
    try
    {
        $var = MarcaDB::find($id);
        $var->load($data);
        $var->save();
        $respuesta["result"] = MarcaDB::find($var->getID());
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(InconsistencyError $ex)
    {
        $respuesta["error"] = "No se pueden realizar los cambios solicitados" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(ExternalError $ex)
    {
        $respuesta["error"] = "Error con la fuente externa\n" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT));
    return $response->withHeader("Content-Type","application/json");
});

$app->delete($path."/{id}", function(Request $request, Response $response, array $args){
    $id = $args["id"];
    try
    {
        MarcaDB::delete($id);
        $respuesta["result"] = "El elemento ya no esta en la base de datos";
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(InconsistencyError $ex)
    {
        $respuesta["error"] = "No se pueden realizar los cambios solicitados" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(ExternalError $ex)
    {
        $respuesta["error"] = "Error con la fuente externa\n" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT));
    return $response->withHeader("Content-Type","application/json");
});

