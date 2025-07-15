<?php

namespace oihana\logging\monolog\processors;

use Monolog\Level;
use Monolog\LogRecord;

class EmojiProcessor
{
    private array $emojiMap =
    [
        Level::Debug->value     => '🐛' , // 100
        Level::Info->value      => 'ℹ' , // 200
        Level::Notice->value    => '📢' , // 250
        Level::Warning->value   => '⚠' , // 300
        Level::Error->value     => '❌' , // 400
        Level::Critical->value  => '💥' , // 500
        Level::Alert->value     => '🚨' , // 550
        Level::Emergency->value => '🆘' , // 600
    ];

    /**
     * Invoke the processor.
     * @param LogRecord $record The log record reference.
     * @return LogRecord
     */
    public function __invoke( LogRecord $record ): LogRecord
    {
        $logLevelValue = $record['level'] ?? Level::Debug->value;

        if( isset( $this->emojiMap[ $logLevelValue ] ) )
        {
            $emoji = $this->emojiMap[ $logLevelValue ] ;
        }
        else
        {
            $emoji = $this->emojiMap[ $logLevelValue ] ?? $record['level_name'] ?? '' ;
        }

        $record['extra']['level_emoji'] = $emoji;

        return $record;
    }
}