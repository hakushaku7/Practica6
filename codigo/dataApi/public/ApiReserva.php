<?php


use App\elements\ReservaDB;
use ExternalAccess\exceptions\{ElementNotFound, ExternalError, InconsistencyError};
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
$path = "/reserva";

$app->get($path, function(Request $request, Response $response)
{
    $respuesta = [];
    try
    {
        $respuesta["result"] = ReservaDB::get();
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

        $respuesta["result"] = ReservaDB::find($id);
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe.\n" . $ex->getMessage();
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
    $var = ReservaDB::placeholder();
    $respuesta = [];
    $var->load($data);
    try
    {
        $var->save();
        $respuesta["result"] = ReservaDB::find($var->getID());
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe.\n" . $ex->getMessage();
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
        $var = ReservaDB::find($id);
        $var->load($data);
        $var->save();
        $respuesta["result"] = ReservaDB::find($var->getID());
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe.\n" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(InconsistencyError $ex)
    {
        $respuesta["error"] = "No se pueden realizar los cambios solicitados.\n" . $ex->getMessage();
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
        ReservaDB::delete($id);
        $respuesta["result"] = "El elemento ya no esta en la base de datos";
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe.\n" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(InconsistencyError $ex)
    {
        $respuesta["error"] = "No se pueden realizar los cambios solicitados.\n" . $ex->getMessage();
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
