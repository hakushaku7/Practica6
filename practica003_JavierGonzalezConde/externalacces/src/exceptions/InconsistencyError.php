<?php
namespace ExternalAccess\exceptions;
require_once __DIR__ . "/ExternalError.php";
/**
 * @extends parent<ExternalError> 
 * Excecepción que indica que un cambio que se ha intentado hacer no es posible ya que probocaría inconsistencias en los datos almacenados
 * 
 * @author Javier González Conde
 * @version 1.0
 */
class InconsistencyError extends ExternalError {};