<?php
namespace ExternalAccess\general;
require_once __DIR__ . "/../../loaders/load_exceptions.php";
use ExternalAccess\exceptions\{ExternalError, FormatError, EntityNotFound};

/**
 * Interfaz que identifica un {@link Elemento} como filtrable, es decir, que se puede acceder a los elementos existentes filtrando por diferentes cacarteristicas.
 * Suele usarse junto con una implementación de la interfaz {@link Filtrador}
 * @see Elemento
 * 
 * @author Javier González Conde
 * @version 1.0
 */
interface Filtrable
{

    /**
     * Filtra y devuelve los elementos existentes de la clase en la funte externa en función de criterios determinados
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws FormatError Los criterios de filtrado no tienen el formato correcto
     * 
     * @param array $filters Criterios de filtrado
     * @return array que contiene la listra filtrada de elementos
     * @since 1.0
     */
    public static function getFiltered(array $filters): array;
}