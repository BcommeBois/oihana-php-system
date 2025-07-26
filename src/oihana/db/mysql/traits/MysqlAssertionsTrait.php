<?php

namespace oihana\db\mysql\traits;

use InvalidArgumentException;

trait MysqlAssertionsTrait
{
    /**
     * Validates a database/user identifier (letters, numbers, underscores only).
     *
     * @param string $name
     * @return void
     * @throws InvalidArgumentException if invalid
     */
    protected function assertIdentifier(string $name): void
    {
        if ( !preg_match('/^[a-zA-Z0-9_]+$/' , $name ) )
        {
            throw new InvalidArgumentException("Invalid identifier: $name" ) ;
        }
    }

    /**
     * Validates a host string (letters, numbers, dots, hyphens).
     *
     * @param string $host
     * @return void
     * @throws InvalidArgumentException if invalid
     */
    protected function assertHost(string $host): void
    {
        if ( !preg_match('/^[\w\.\-%]+$/' , $host ) )
        {
            throw new InvalidArgumentException("Invalid host: $host" ) ;
        }
    }
}