<?php

namespace oihana\db\mysql;

use oihana\enums\Char;
use function oihana\core\strings\snake;

class MysqlDSN
{
    /**
     * Creates a new OpenEdgeDSN instance.
     * @param array $init The init object to defines the DSN expression.
     */
    public function __construct( array $init = [] )
    {
        $this->charset    = $init[ self::CHARSET     ] ?? 'utf8mb4' ;
        $this->dbname     = $init[ self::DBNAME      ] ?? null ;
        $this->host       = $init[ self::HOST        ] ?? 'localhost' ;
        $this->port       = $init[ self::PORT        ] ?? 3306 ;
        $this->unixSocket = $init[ self::UNIX_SOCKET ] ?? null ;
    }

    const string HOST        = 'host' ;
    const string PORT        = 'port' ;
    const string DBNAME      = 'dbname' ;
    const string CHARSET     = 'charset' ;
    const string UNIX_SOCKET = 'unixSocket' ;

    /**
     * Character set.
     * @var string
     */
    public string $charset ;

    /**
     * Database name.
     * @var ?string
     */
    public ?string $dbname ;

    /**
     * Hostname or IP of the MySQL server.
     * @var string
     */
    public string $host ;

    /**
     * Port number to connect to.
     * @var int
     */
    public int $port ;

    /**
     * Optional Unix socket path.
     * @var ?string
     */
    public ?string $unixSocket ;

    /**
     * Converts the DSN object to an associative array.
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return
        [
            self::HOST        => $this->host ,
            self::PORT        => $this->port ,
            self::DBNAME      => $this->dbname ,
            self::CHARSET     => $this->charset ,
            self::UNIX_SOCKET => $this->unixSocket ,
        ];
    }

    /**
     * Converts the object to its DSN string representation.
     * @return string
     */
    public function __toString(): string
    {
        $dsn = "mysql:" ;
        $params = [];

        $params[] = self::HOST . Char::EQUAL . $this->host ;
        $params[] = self::PORT . Char::EQUAL . $this->port ;

        if ( $this->dbname !== null )
        {
            $params[] = self::DBNAME . Char::EQUAL . $this->dbname ;
        }

        $params[] = self::CHARSET . Char::EQUAL . $this->charset ;

        if ( $this->unixSocket !== null )
        {
            $params[] = snake( self::UNIX_SOCKET ) . Char::EQUAL . $this->unixSocket ;
        }

        return $dsn . implode( Char::SEMI_COLON, $params );
    }
}