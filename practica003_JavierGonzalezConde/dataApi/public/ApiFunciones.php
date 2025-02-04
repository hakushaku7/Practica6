<?php

use App\elements\ReservaDB;
use App\elements\VehiculoDB;
use ExternalAccess\exceptions\{ElementAlreadyExists, ElementNotFound, ExternalError, InconsistencyError};
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\api\Utilidades;



$app->get("/consultarstock/{maxkm}/{marca}/{reserva}", function(Request $request, Response $response, $args)
{
    $respuesta = [];
    $data =  $args;
    
    $respuesta["filtro"] = $data;
    $json = false;
    $xml = false;
    foreach($request->getHeaders()["Accept"] as $accept)
        switch(strtolower($accept))
        {
            case "application/json":
            case "json":
                $json = true;
                break;
            
            case "application/xml":
            case "xml":
                    $xml = true;
                    break;
        }
    $mode = (!$json and $xml) ? "xml": "json";
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

    if($mode == "xml")
        $response->getBody()->write(Utilidades::array2xml($respuesta));
    else 
        $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT));
    
    return $response->withHeader("Content-Type","application/$mode");
});


$app->post("/realizarreserva", function (Request $request, Response $response)
{
    
    $data = $request->getParsedBody();

    $respuesta = ["ok"=> false];

    if(isset($data["nombre"]) and isset($data["apellidos"]) and isset($data["dni"]) and isset($data["matricula"]))
        try
        {
            $vehiculo = VehiculoDB::find_matricula($data["matricula"]);
            if($vehiculo->reservado())
                $respuesta["error"] = "vehiculo ya reservado";
            else
            {
                $data["id"] = 0;
                $reserva = new ReservaDB();
                $reserva->load($data);
                $reserva->setVehiculo($vehiculo);
                $reserva->save();
                $respuesta["ok"] = "true";
                $respuesta["mensage"] = "Se ha realizado la reserva";
                $respuesta["result"] = $reserva;
            }
        }
        catch(ElementNotFound $e)
        {
            $respuesta["error"] = $e->getMessage();
        }
        catch(InconsistencyError $e)
        {
            $respuesta["error"] = $e->getMessage();
        }
        catch(ExternalError $e)
        {
            $respuesta["error"] = $e->getMessage();
        }
    else
        $respuesta["error"] = "Los datos indicados no están en el formato correcto";
    $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT));
    return $response->withHeader("Content-Type","application/json");
});


$app->delete("/liberarreserva/{id}", function(Request $request, Response $response, array $args){
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




$app->get("/buscarmatricula/{matricula}", function(Request $request, Response $response, array $args){
    $matricula = $args["matricula"];
    $respuesta = [];
    try
    {

        $respuesta["result"] = VehiculoDB::find_matricula($matricula);
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


