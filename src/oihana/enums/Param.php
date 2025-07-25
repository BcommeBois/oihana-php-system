<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Centralized enumeration of parameter keys used across various parts of the application.
 *
 * This class defines a large set of constants to avoid hard-coded string keys,
 * enabling safer and more consistent parameter usage throughout your codebase.
 *
 * Example usage:
 * ```php
 * use oihana\enums\Param;
 *
 * $options = [
 *     Param::LIMIT   => 20,
 *     Param::OFFSET  => 0,
 *     Param::ORDER   => 'DESC',
 *     Param::FILTER  => ['status' => 'active'],
 * ];
 * ```
 *
 * This enumeration can be used in:
 * - HTTP request parsers
 * - Configuration arrays
 * - Filtering and sorting logic
 * - CLI arguments
 * - Custom annotations or metadata
 *
 * Optionally, you can group constants by context using a utility method like `groupByPrefix()`.
 *
 * @package oihana\enums
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class Param
{
    use ConstantsTrait ;

    public const string ACTION                    = 'action' ;
    public const string ACTIONS                   = 'actions' ;
    public const string ACTIVE                    = 'active' ;
    public const string ACTIVABLE                 = 'activable' ;
    public const string AFTER                     = 'after' ;
    public const string ALL                       = 'all' ;
    public const string ALIAS                     = 'alias' ;
    public const string ALT                       = 'alt' ;
    public const string ALTER                     = 'alter' ;
    public const string ALTERS                    = 'alters' ;
    public const string ARGS                      = 'args' ;
    public const string BASE_PATH                 = 'basePath' ;
    public const string BATCH_SIZE                = 'batchSize' ;
    public const string BEFORE                    = 'before' ;
    public const string BENCH                     = 'bench' ;
    public const string BINDS                     = 'binds' ;
    public const string BOOL                      = 'bool' ;
    public const string CACHE                     = 'cache' ;
    public const string CACHEABLE                 = 'cacheable' ;
    public const string COMPARATOR                = 'comparator' ;
    public const string COMPRESS                  = 'compress' ;
    public const string CONDITION                 = 'condition' ;
    public const string CONDITIONS                = 'conditions' ;
    public const string CONFIG                    = 'config' ;
    public const string COLLECTION                = 'collection' ;
    public const string CONTEXT                   = 'context' ;
    public const string CONTROLLER                = 'controller' ;
    public const string CONTROLLERS               = 'controllers' ;
    public const string CUSTOM_RULES              = 'customRules' ;
    public const string DATE                      = 'date' ;
    public const string DATE_FORMAT               = 'dateFormat' ;
    public const string DEBUG                     = 'debug' ;
    public const string DEFAULT                   = 'default' ;
    public const string DESCRIPTION               = 'description' ;
    public const string DEFER_ASSIGNMENT          = 'deferAssignment' ;
    public const string DEFINITION                = 'definition' ;
    public const string DESTROY                   = 'destroy' ;
    public const string DIRECTION                 = 'direction' ;
    public const string DIRECTORY                 = 'directory' ;
    public const string DOC                       = 'doc' ;
    public const string DOC_REF                   = 'docRef' ;
    public const string DOCS                      = 'docs' ;
    public const string DOCUMENT_KEY              = 'documentKey' ;
    public const string DOCUMENTS                 = 'documents' ;
    public const string ENABLE                    = 'enable' ;
    public const string ENABLED                   = 'enabled' ;
    public const string ENCRYPT                   = 'encrypt' ;
    public const string EXCLUDES                  = 'excludes' ;
    public const string EXIST                     = 'exist' ;
    public const string EXTENSIONS                = 'extensions' ;
    public const string EXTRA_QUERY               = 'extraQuery' ;
    public const string FACET                     = 'facet' ;
    public const string FACETS                    = 'facets' ;
    public const string FILE                      = 'file' ;
    public const string FILTER                    = 'filter' ;
    public const string FILTERABLE                = 'filterable' ;
    public const string FILTER_KEYS               = 'filterKeys' ;
    public const string FILTER_PATCH_KEYS         = 'filterPatchKeys' ;
    public const string FILTER_PATCH_AND_PUT_KEYS = 'filterPatchAndPutKeys' ;
    public const string FILTER_POST_KEYS          = 'filterPostKeys' ;
    public const string FILTER_PUT_KEYS           = 'filterPutKeys' ;
    public const string FIELD                     = 'field' ;
    public const string FIELDS                    = 'fields' ;
    public const string FORCE_URL                 = 'forceUrl' ;
    public const string FORMAT                    = 'format' ;
    public const string FULL_PATH                 = 'fullPath' ;
    public const string GROUP_BY                  = 'groupBy' ;
    public const string HARVEST                   = 'harvest' ;
    public const string HAS_TOTAL                 = 'hasTotal' ;
    public const string HELP                      = 'help' ;
    public const string HTML                      = 'html' ;
    public const string I18N                      = 'i18n' ;
    public const string ID                        = 'id' ;
    public const string IDS                       = 'ids' ;
    public const string INFLECTOR                 = 'inflector' ;
    public const string INIT                      = 'init' ;
    public const string INSERT                    = 'insert' ;
    public const string INTERVAL                  = 'interval' ;
    public const string INTERVAL_DEFAULT          = 'interval_default' ;
    public const string ITEMS                     = 'items' ;
    public const string JOIN                      = 'join' ;
    public const string JOINS                     = 'joins' ;
    public const string JSON                      = 'json' ;
    public const string JSON_OPTIONS              = 'jsonOptions' ;
    public const string KEY                       = 'key' ;
    public const string KEY_LIST                  = 'keyList' ;
    public const string LANG                      = 'lang' ;
    public const string LAST                      = 'last' ;
    public const string LAZY                      = 'lazy' ;
    public const string LIMIT                     = 'limit' ;
    public const string LIMIT_DEFAULT             = 'limit_default' ;
    public const string LIST                      = 'list' ;
    public const string LOCATION                  = 'location' ;
    public const string LOCATIONS                 = 'locations' ;
    public const string LOCKABLE                  = 'lockable' ;
    public const string LOGGABLE                  = 'loggable' ;
    public const string LOGGER                    = 'logger' ;
    public const string MARGIN                    = 'margin' ;
    public const string MATCH                     = 'match' ;
    public const string MAX_DEPTH                 = 'maxDepth' ;
    public const string MAX_INTERVAL              = 'maxInterval' ;
    public const string MAX_LIMIT                 = 'maxLimit' ;
    public const string MAX_RANGE                 = 'max_range' ;
    public const string MEMCACHED                 = 'memcached' ;
    public const string METHOD                    = 'method' ;
    public const string METHODS                   = 'methods' ;
    public const string MIN_LIMIT                 = 'minLimit' ;
    public const string MIN_RANGE                 = 'min_range' ;
    public const string MOCK                      = 'mock' ;
    public const string MODEL                     = 'model' ;
    public const string MODELS                    = 'models' ;
    public const string MODEL_ID                  = 'modelID' ;
    public const string NAME                      = 'name' ;
    public const string DEFAULT_NOT_EQUALS        = 'defaultNotEquals' ;
    public const string NOT_EQUALS                = 'notEquals' ;
    public const string NUM                       = 'num' ;
    public const string OBJECT                    = 'object' ;
    public const string OP                        = 'op' ;
    public const string OPTIONS                   = 'options' ;
    public const string OFFSET                    = 'offset' ;
    public const string OFFSET_DEFAULT            = 'offset_default' ;
    public const string ORDER                     = 'order' ;
    public const string OPTIMIZED                 = 'optimized' ;
    public const string OVERWRITE                 = 'overwrite' ;
    public const string OWNER                     = 'owner' ;
    public const string OWNER_KEY                 = 'ownerKey' ;
    public const string OWNERS                    = 'owners' ;
    public const string OWNER_PATH                = 'ownerPath' ;
    public const string PASS_PHRASE               = 'passphrase' ;
    public const string PATH                      = 'path' ;
    public const string PATTERN                   = 'pattern' ;
    public const string PDO                       = 'pdo' ;
    public const string POSITION                  = 'position' ;
    public const string POST_INIT                 = 'postInit' ;
    public const string PRE_INIT                  = 'preInit' ;
    public const string PRECISION                 = 'precision' ;
    public const string PRETTY_PRINT              = 'pretty_print' ;
    public const string PREFIX                    = 'prefix' ;
    public const string PROPERTY                  = 'property' ;
    public const string PROPERTIES                = 'properties' ;
    public const string PROPS                     = 'props' ;
    public const string PARAMS                    = 'params' ;
    public const string PARAMS_STRATEGY           = 'paramsStrategy' ;
    public const string PASSWORD                  = 'password' ;
    public const string QUANTITY                  = 'quantity' ;
    public const string QUERY                     = 'query' ;
    public const string QUERY_BUILDER             = 'queryBuilder' ;
    public const string QUERY_FIELDS              = 'queryFields' ;
    public const string QUERY_ID                  = 'queryId' ;
    public const string REDIRECTS                 = 'redirects' ;
    public const string RELATIONS                 = 'relations' ;
    public const string RETURN                    = 'return' ;
    public const string REVERSE                   = 'reverse' ;
    public const string ROUTE                     = 'route' ;
    public const string RULE                      = 'rule' ;
    public const string RULES                     = 'rules' ;
    public const string SCHEMA                    = 'schema' ;
    public const string SEARCH                    = 'search' ;
    public const string SIDE                      = 'side' ;
    public const string SILENT                    = 'silent' ;
    public const string SINGLE                    = 'single' ;
    public const string SKIN                      = 'skin' ;
    public const string SKIN_DEFAULT              = 'skinDefault' ;
    public const string SKIN_FROM                 = 'skinFrom' ;
    public const string SKIN_METHODS              = 'skinMethods' ;
    public const string SKIN_PREFIX               = 'skin_' ;
    public const string SKINS                     = 'skins' ;
    public const string SKIP                      = 'skip' ;
    public const string SORT                      = 'sort' ;
    public const string SORTABLE                  = 'sortable' ;
    public const string SORT_DEFAULT              = 'sortDefault' ;
    public const string STATUS                    = 'status' ;
    public const string STATUS_NAME               = 'statusName' ;
    public const string STRICT                    = 'strict' ;
    public const string THINGS                    = 'things' ;
    public const string TABLE                     = 'table' ;
    public const string TIMEZONE                  = 'timezone' ;
    public const string TIMEZONE_DEFAULT          = 'timezone_default' ;
    public const string TOTAL                     = 'total' ;
    public const string TYPE                      = 'type' ;
    public const string UNIQUE                    = 'unique' ;
    public const string UPDATE                    = 'update' ;
    public const string URL                       = 'url' ;
    public const string USERNAME                  = 'username' ;
    public const string VALIDATOR                 = 'validator' ;
    public const string VAR_NAME                  = 'varName' ;
    public const string VAL                       = 'val' ;
    public const string VALIDATE                  = 'validate' ;
    public const string VALUE                     = 'value' ;
    public const string VALUES                    = 'values' ;
    public const string VERBOSE                   = 'verbose' ;
    public const string WITH                      = 'with' ;

    /**
     * Returns an associative array of constants whose names start with the given prefix.
     *
     * This is useful for grouping related parameters based on naming convention,
     * such as all constants starting with "filter", "json", or "skin".
     *
     * The returned array preserves constant names as keys and their corresponding values.
     *
     * Example:
     * ```php
     * Param::groupByPrefix('FILTER_');
     * // [
     * //     'FILTER'                  => 'filter',
     * //     'FILTER_KEYS'            => 'filterKeys',
     * //     'FILTER_POST_KEYS'       => 'filterPostKeys',
     * //     ...
     * // ]
     * ```
     *
     * @param string $prefix The prefix to match against constant names.
     *
     * @return array<string, mixed> An associative array of matching constant names and their values.
     */
    public static function groupByPrefix( string $prefix ): array
    {
        return array_filter( self::getAll() , fn( $key ) => str_starts_with( $key , $prefix ) , ARRAY_FILTER_USE_KEY ) ;
    }
}