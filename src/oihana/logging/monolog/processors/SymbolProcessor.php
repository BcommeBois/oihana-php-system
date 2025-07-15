<?php

namespace oihana\logging\monolog\processors;

use Monolog\Level;
use Monolog\LogRecord;

class SymbolProcessor
{
    /**
     * Creates a new SymbolProcessor.
     * @param bool $useColors Indicates if the level symbol use colors.
     */
    public function __construct( bool $useColors = true )
    {
        $this->useColors = $useColors ;
    }

    public bool $useColors ;

    private array $symbolMap =
    [
        Level::Debug->value     => '›' , // 100
        Level::Info->value      => 'i' , // 200
        Level::Notice->value    => '※' , // 250
        Level::Warning->value   => '▲' , // 300
        Level::Error->value     => '✘' , // 400
        Level::Critical->value  => '⚡' , // 500
        Level::Alert->value     => '‼' , // 550
        Level::Emergency->value => '☢' , // 600
    ];

    private array $colorMap =
    [
        Level::Debug->value     => "\033[37m",    // Gris
        Level::Info->value      => "\033[32m",    // Vert
        Level::Notice->value    => "\033[36m",    // Cyan
        Level::Warning->value   => "\033[33m",    // Jaune
        Level::Error->value     => "\033[31m",    // Rouge
        Level::Critical->value  => "\033[35m",    // Magenta
        Level::Alert->value     => "\033[91m",    // Rouge clair
        Level::Emergency->value => "\033[37;41m", // Blanc sur fond rouge
    ];

    /**
     * Invoke the processor.
     * @param LogRecord $record The log record reference.
     * @return LogRecord
     */
    public function __invoke( LogRecord $record ): LogRecord
    {
        $logLevelValue = $record['level'] ?? Level::Debug->value;

        $emoji = $this->symbolMap[ $logLevelValue ] ?? $record['level_name'] ?? '';

        if ( $this->useColors && isset( $this->colorMap[ $logLevelValue ] ) )
        {
            $colorStart = $this->colorMap[$logLevelValue] ?? "\033[0m";
            $colorEnd = "\033[0m"; // Réinitialiser la couleur
            $emoji = $colorStart . $emoji . $colorEnd;
        }

        $record['extra']['level_emoji'] = $emoji;

        return $record;
    }
}