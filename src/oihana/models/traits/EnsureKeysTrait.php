<?php

namespace oihana\models\traits;

use oihana\models\enums\ModelParam;

use function oihana\core\accessors\ensureKeyValue;
use function oihana\core\arrays\isIndexed;

/**
 * Provides functionality to guarantee the existence of specific keys or properties
 * within document structures or collections.
 *
 * This trait processes the configuration provided in `ModelParam::ENSURE`
 * to automatically populate missing keys with default values.
 *
 * ### Usage example:
 *
 * ```php
 * class MyModel
 * {
 *    use EnsureKeysTrait;
 *
 *    public function fetchAndProcess($init)
 *    {
 *        $data = ['id' => 1];
 *
 *        // Ensures 'status' exists, defaults to 'draft'
 *        $this->ensureDocumentKeys($data, $init);
 *
 *        return $data;
 *     }
 * }
 *
 * // Usage
 * $model->fetchAndProcess
 * ([
 *     ModelParam::ENSURE  =>
 *     [
 *         ModelParam::KEYS    => ['status'],
 *         ModelParam::DEFAULT => 'draft'
 *     ]
 * ]);
 * ```
 *
 * @package oihana\models\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait EnsureKeysTrait
{
    /**
     * Ensures that specific attributes (keys or properties) exist on a document or a collection of documents.
     *
     * It parses the configuration from $init[ModelParam::ENSURE] and applies it to $data.
     * It automatically detects if $data is a collection (indexed array) or a single item.
     *
     * @param mixed &$data Reference to the document(s). Modified in place.
     * @param array $init  The initialization options containing ModelParam::ENSURE.
     *
     * @return void
     */
    protected function ensureDocumentKeys( mixed &$data , array $init = [] ): void
    {
        // 1. Guard Clauses
        if ( empty( $data ) || !isset( $init[ ModelParam::ENSURE ] ) )
        {
            return;
        }

        $config = $init[ ModelParam::ENSURE ] ;

        // 2. Configuration Parsing
        if ( isset( $config[ ModelParam::KEYS ] ) )
        {
            $keys    = $config[ ModelParam::KEYS    ] ;
            $default = $config[ ModelParam::DEFAULT ] ?? null  ;
            $enforce = $config[ ModelParam::ENFORCE ] ?? false ;
        }
        else
        {
            $keys    = $config ;
            $default = null    ;
            $enforce = false   ;
        }

        // 3. Application (Collection vs Single Document)

        if ( is_array( $data ) && isIndexed( $data ) )
        {
            foreach ( $data as &$document )
            {
                $document = ensureKeyValue
                (
                    document: $document ,
                    keys:     $keys     ,
                    default:  $default  ,
                    enforce:  $enforce
                );
            }
        }
        else
        {
            $data = ensureKeyValue
            (
                document : $data    ,
                keys     : $keys    ,
                default  : $default ,
                enforce  : $enforce
            ) ;
        }
    }
}