<?php

namespace oihana\db\mysql;

use InvalidArgumentException;
use oihana\enums\Char;
use PDO;

use oihana\traits\ToStringTrait;

class MysqlPDOBuilder
{
    /**
     * Creates a new MySQLPDOBuilder instance.
     * @param array $init Configuration parameters.
     */
    public function __construct( array $init = [] )
    {
        $this->dsn        = new MySQLDSN( $init ) ;
        $this->username   = $init[ self::USERNAME ] ?? null ;
        $this->password   = $init[ self::PASSWORD ] ?? null ;
        $this->skipDbName = $init[ self::SKIP_DB_NAME ] ?? false ;
        $this->options    = [ ...self::DEFAULT_OPTIONS , ...( $init[ self::OPTIONS  ] ?? [] ) ] ;
        $this->validate   = $init[ self::VALIDATE ] ?? true ;
    }

    public const string OPTIONS      = 'options' ;
    public const string PASSWORD     = 'password' ;
    public const string SKIP_DB_NAME = 'skipDbName' ;
    public const string USERNAME     = 'username' ;
    public const string VALIDATE     = 'validate' ;

    public const array DEFAULT_OPTIONS =
    [
        PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC ,
        PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION ,
        PDO::ATTR_PERSISTENT          => true,
        PDO::ATTR_EMULATE_PREPARES    => false,
        PDO::ATTR_STRINGIFY_FETCHES   => false,
    ];

    use ToStringTrait ;

    /**
     * The DSN wrapper.
     * @var MySQLDSN
     */
    public MySQLDSN $dsn ;

    /**
     * Additional PDO options.
     * @var array
     */
    public array $options = [];

    /**
     * The user password.
     * @var ?string
     */
    public ?string $password ;

    /**
     * Indicates if the validation of the dbname is skipped.
     * @var bool
     */
    public bool $skipDbName = false ;

    /**
     * The database user.
     * @var ?string
     */
    public ?string $username ;

    /**
     * If true, the connection will be validated before use.
     * @var bool
     */
    public bool $validate = true;

    /**
     * Returns a PDO instance.
     * @return ?PDO
     */
    public function __invoke(): ?PDO
    {
        if ( $this->validate )
        {
            $this->validate();
        }

        return new PDO
        (
            (string) $this->dsn,
            $this->username ,
            $this->password ,
            $this->options  ,
        ) ;
    }

    /**
     * Returns the full configuration as array (dsn + credentials + options).
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'dsn'      => $this->dsn->toArray(),
            'username' => $this->username,
            'password' => str_repeat(Char::ASTERISK , strlen( ( string ) $this->password ) ), // Hidden
            'options'  => $this->options,
            'validate' => $this->validate,
        ];
    }

    /**
     * Validates required DSN fields.
     * @return void
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        if ( !$this->dsn->host )
        {
            throw new InvalidArgumentException('MySQL DSN is missing the host.' ) ;
        }

        if ( !$this->dsn->dbname && !$this->skipDbName )
        {
            throw new InvalidArgumentException('MySQL DSN is missing the database name.' ) ;
        }

        if ( !$this->username )
        {
            throw new InvalidArgumentException('MySQL connection requires a username.' ) ;
        }
    }
}