<?php
namespace ExternalAccess\exceptions;
require_once __DIR__ . "/ExternalError.php";
/**
 * @extends parent<ExternalError>
 * Excepción que indica que se ha intentado acceder a una clase que no existe en la fuente externa
 * 
 * @author Javier González Conde
 * @version 1.0
 */
class EntityNotFound extends ExternalError {};