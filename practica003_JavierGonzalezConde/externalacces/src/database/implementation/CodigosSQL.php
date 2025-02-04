<?php
namespace ExternalAccess\database\implementation;

/**
 * Código generico de SQL que en principio es aplicable a cualquier base de datos que siga unas normas de estilo genericas, como que las claves de las entidades sean siempre identificadores numericos autoincrementados con el nombre "id".
 * 
 * @author Javier González Conde
 * @version 1.0
 */
class CodigosSQL
{
    /**
     * PARCIAL: SELECT =>
     * Cuenta el numero de elementos existentes en una consulta
     * 
     * @var string
     * @since 1.0
     */
    public const COUNT ="SELECT count(*) as result";

    /**
     * COMPLETA =>
     * Determina si una tabla existe en la base de datos
     * [1-2?] Nombre de la tabla 
     * 
     * @var string
     * @since 1.0
     */
    public const CHECK_TABLA = "SELECT count(*) as result FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;";
    
    /**
     * PARCIAL: WHERE=>
     * Filtra los elementos de una consulta por su id
     * [1?] id del elemento
     * 
     * @var string
     * @since 1.0
     */
    public const FILTER_ID = " id = ? ";

    //KEYWORDS
    /**
     * KEYWORD: TIPO=>
     * Obtiene información de la base de datos
     * 
     * @var string
     * @since 1.0
     */
    public const SELECT = "SELECT ";

    /**
     * KEYWORD: TIPO=>
     * Sobrescribe de la base de datos
     * 
     * @var string
     * @since 1.0
     */
    public const UPDATE = "UPDATE ";

    /**
     * KEYWORD: TIPO=>
     * Añade información de la base de datos
     * 
     * @var string
     * @since 1.0
     */
    public const INSERT = "INSERT INTO ";

    /**
     * KEYWORD: TIPO=>
     * Elimina información de la base de datos
     * 
     * @var string
     * @since 1.0
     */
    public const BORRAR = "DELETE FROM ";

    /**
     * KEYWORDS: SECCIONES=>
     * Inicio del filtrado de una consulta
     * 
     * @var string
     * @since 1.0
     */
    public const FILTER = " WHERE ";

    /**
     * KEYWORDS: SECCIONES=>
     * Inicio de la importación de tablas
     * 
     * @var string
     * @since 1.0
     */
    public const FROM = " FROM ";

    /**
     * KEYWORDS: SECCIONES=>
     * Marca el fin de la consulta
     * 
     * @var string
     * @since 1.0
     */
    public const END = ";";
    
    /**
     * KEYWORD: COMPLEMENTOS=>
     * Especifica los capos que se van a añadir a la base de datos
     * 
     * @var string
     * @since 1.0
     */
    public const VALUES = " VALUES (";

    /**
     * KEYWORD: MODIFICADOR=>
     * Reasigna el nombre de los elementos en la consulta
     * 
     * @var string
     * @since 1.0
     */
    public const AS = " AS ";

    /**
     * KEYWORD: ACTUALIZADOR=>
     * Establece los campos y los valores que se modificarán
     * 
     * @var string
     * @since 1.0
     */
    public const SET = " SET "; 
    
    /**
     * KEYWORD: OPERADOR=>
     * Operador logico "and"
     * 
     * @var string
     * @since 1.0
     */
    public const AND = " AND ";

    /**
     * KEYWORD: OPERADOR=>
     * Operador logico "or"
     * 
     * @var string
     * @since 1.0
     */
    public const OR = " OR ";

    /**
     * KEYWORD: OPERADOR=>
     * Comprueba si dos valores son iguales o asigna uno de ellos
     * 
     * @var string
     * @since 1.0
     */
    public const EQUALS = " = ";

    /**
     * KEYWORD: OPERADOR=>
     * Comprueba si un campo es nulo
     * 
     * @var string
     * @since 1.0
     */
    public const IS_NULL =" IS NULL ";

    /**
     * KEYWORD: OPERADOR=>
     * Comprueba si un campo no es nulo
     * 
     * @var string
     * @since 1.0
     */
    public const NOT_NULL = "IS NOT NULL";
}

