<?php
namespace ExternalAccess\exceptions;
require_once __DIR__ . "/InconsistencyError.php";
/**
 * Excepción que indica que se ha intentado crear un elemento que ya existe.
 * 
 * @author Javier González Conde
 * @version 1.0
 * @extends parent<InconsistencyError> Especifica que la inconsistencia se debe a que ya existe un elemento con las propiedades que se quieren guardar
 */
class ElementAlreadyExists extends InconsistencyError {};