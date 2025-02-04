<?php
namespace ExternalAccess\exceptions;

require_once __DIR__ . "/ExternalError.php";
/**
 * Indica un error general con el acceso a la base de datos 
 * 
 * @author Javier González Conde
 * @version 1.0
 * @extends parent<ExternalError> Especificado para base de datos
 */
class DatabaseError extends  ExternalError {};