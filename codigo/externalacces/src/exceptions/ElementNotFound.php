<?php
namespace ExternalAccess\exceptions;


require_once __DIR__ . "/ExternalError.php";
/**
 * @extends parent<ExternalError>
 * 
 * Excepción que indica que se ha intentado obtener un elemento que no existe en la base de datos
 * 
 * @author Javier González Conde
 * @version 1.0
 */
class ElementNotFound extends  ExternalError {};