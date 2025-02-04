<?php
namespace ExternalAccess\general;
require_once __DIR__ . "/../../loaders/load_exceptions.php";
use ExternalAccess\exceptions\{ExternalError, FormatError, EntityNotFound};

/**
 * Interfaz que permite a una instancia de {@link DataAccess} que le permite filtrar elementos de la interfaz {@link Elemento}
 * Suele usarse junto con la interfaz {@link Filtrable}
 * 
 * @author Javier González Conde
 * @version 1.1
 */
interface Filtrador
{
    /**
     * Permite acceder a los elementos de una clase de forma similar a el metodo {@link DacaAccess::get()}, pero filtrando los elementos devueltos.
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws FormatError Los criterios de filtrado no tienen el formato correcto
     * 
     * @param Elemento $elemento Clase de los elementos que queremos filtrar
     * @param array $filters Filtros que se quiere aplicar al filtrado
     * @return array Elementos obtenidos tras aplicar el filtrado
     * @since 1.1
     */
    public static function getFiltered(Elemento $elemento, array $filters): array;

    /**
     * @deprecated Sustituido por {@link Filtrador::getFiltered()}
     * Se usa para filtrar elementos de la clase filtrable
     * 
     * @throws ExternalError Problema al acceder a la fuente externa
     * @throws EntityNotFound La clase introducida no existe o no es compatible con la forma externa
     * @throws FormatError Los criterios de filtrado no tienen el formato correcto
     * 
     * @param Filtrable $elemento Clase de los elementos que queremos filtrar
     * @param array $filters Filtros que se quiere aplicar al filtrado
     * @return array Elementos obtenidos tras aplicar el filtrado
     * @since 1.0
     */
    public static function get_filtered(Filtrable $elemento, array $filters): array;


}