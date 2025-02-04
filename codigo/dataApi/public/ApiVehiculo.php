<?php


use App\elements\VehiculoDB;
use ExternalAccess\exceptions\{ElementAlreadyExists, ElementNotFound, ExternalError, InconsistencyError};
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
$path = "/vehiculo";

$app->get($path, function(Request $request, Response $response)
{
    $respuesta = [];
    $data =  $request->getParsedBody();
    $respuesta["filtro"] = $data;
    try
    {
        if($data != null)
            $respuesta["result"] = VehiculoDB::get_filtered($data);
        else
            $respuesta["result"] = VehiculoDB::get();
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

        $respuesta["result"] = VehiculoDB::find((int)$id);
        $respuesta["ok"] = True;
    }
    catch(ElementNotFound $ex)
    {
        $respuesta["error"] = "El elemento buscado no existe.\n" . $ex->getMessage();
        $respuesta["ok"] = False;
    }
    catch(ElementAlreadyExists $ex)
    {
        $respuesta["error"] = $ex->getMessage();
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
    $var = VehiculoDB::placeholder();
    $respuesta = [];
    $var->load($data);
    try
    {
        $var->save();
        $respuesta["result"] = VehiculoDB::find($var->getID());
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
        $var = VehiculoDB::find($id);
        $var->load($data);
        $var->save();
        $respuesta["result"] = VehiculoDB::find($var->getID());
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
        VehiculoDB::delete($id);
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


