<?php

namespace oihana\controllers\enums ;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Enumeration of the option keys accepted by {@see oihana\controllers\traits\ArchiveTrait::extractZip()}.
 *
 * @package oihana\controllers\enums
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ExtractOption
{
    use ConstantsTrait ;

    /**
     * Maximum number of entries allowed in the archive (decompression-bomb guard). Default: no limit.
     */
    public const string MAX_ENTRIES = 'maxEntries' ;

    /**
     * Maximum total uncompressed size in bytes (decompression-bomb guard). Default: no limit.
     */
    public const string MAX_SIZE = 'maxSize' ;

    /**
     * Whether an existing target file may be overwritten. Default: false.
     */
    public const string OVERWRITE = 'overwrite' ;
}
