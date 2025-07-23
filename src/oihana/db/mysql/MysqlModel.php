<?php

namespace oihana\db\mysql;

use DI\Container;
use InvalidArgumentException;
use oihana\enums\Param;
use oihana\models\pdo\PDOModel;
use PDO;
use PDOException;
use PDOStatement;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * MysqlModel provides high-level MySQL administrative operations using PDO.
 *
 * It allows you to:
 * - Create and drop MySQL databases and users.
 * - Grant or revoke privileges.
 * - Inspect privilege assignments (grants).
 * - Validate identifiers and host syntax.
 *
 * Requires a properly connected PDO instance with sufficient privileges.
 *
 * @package oihana\db\mysql
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 *
 * @example
 * ```php
 * $model = new MysqlModel();
 *
 * $model->setPDO( $pdoAdmin ) ; // Connect as root or admin user
 *
 * $model->createDatabase('my_app');
 * $model->createUser('myuser', 'localhost', 'securepass');
 * $model->grantPrivileges('myuser', 'localhost', 'my_app');
 * $model->flushPrivileges();
 *
 * // Rename the user
 * $model->renameUser('myuser', 'localhost', 'user', 'localhost');
 *
 * // Revoke the privilege of the database.
 * $model->revokePrivileges('user', 'localhost', 'myapp');
 *
 * // Export the database informations.
 * print_r( $model->toArray() ) ;
 * ```
 *
 * if (!$model->databaseExists('myapp'))
 * {
 *    $model->createDatabase('myapp');
 * }
 *
 * if ( !$model->userExists('admin', 'localhost') )
 * {
 *      $model->createUser('admin', 'localhost', 'strongpass');
 * }
 */
class MysqlModel extends PDOModel
{
    /**
     * Creates a new MysqlModel instance.
     *
     * @param Container $container The DI container to retrieve services like PDO and logger.
     * @param array $init Optional initialization array with keys:
     * - Param::ALTERS: array of alterations to apply
     * - Param::BINDS: array of binds for queries
     * - Param::DEFER_ASSIGNMENT: bool whether to defer property assignment on fetch
     * - Param::SCHEMA: string class name of schema for fetch mode
     * - Param::PDO: PDO instance or service name in container
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct( Container $container , array $init = [] )
    {
        parent::__construct( $container , $init ) ;
    }

    /**
     * Creates a new MySQL database with given charset and collation.
     *
     * @param string $name       The name of the database.
     * @param string $charset    The character set (default: 'utf8').
     * @param string $collation  The collation (default: 'utf8_general_ci').
     * @return bool              True on success, false otherwise.
     */
    public function createDatabase( string $name , string $charset = 'utf8' , string $collation = 'utf8_general_ci' ): bool
    {
        $this->assertIdentifier( $name ) ;

        $query = sprintf
        (
            "CREATE DATABASE IF NOT EXISTS `%s` DEFAULT CHARACTER SET %s DEFAULT COLLATE %s",
            $name, $charset, $collation
        );

        return $this->pdo?->exec( $query ) !== false;
    }

    /**
     * Creates a new MySQL user with a password.
     *
     * @param string $username  The username to create.
     * @param string $host      The host (default: 'localhost').
     * @param string $password  The password for the user.
     * @return bool             True on success, false otherwise.
     */
    public function createUser( string $username, string $host = 'localhost', string $password = '' ): bool
    {
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;

        $query = sprintf("CREATE USER IF NOT EXISTS '%s'@'%s' IDENTIFIED BY :password" , $username , $host ) ;

        $statement = $this->pdo?->prepare( $query ) ;

        $output = false ;

        if( $statement instanceof PDOStatement )
        {
            $this->bindValues( $statement , [ 'password' => $password ] );
            $output = $statement->execute() ;
        }

        $statement = null ;
        return $output ;
    }

    /**
     * Checks if a MySQL database exists.
     *
     * @param string $name  Database name.
     * @return bool         True if the database exists.
     */
    public function databaseExists( string $name ): bool
    {
        $this->assertIdentifier( $name );

        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :name";
        $stmt  = $this->pdo?->prepare($query);

        if (!$stmt)
        {
            return false ;
        }

        $this->bindValues($stmt, [ 'name' => $name ] );

        return $stmt->execute() && $stmt->fetchColumn() !== false;
    }

    /**
     * Drops a database if it exists.
     *
     * @param string $name  The name of the database to drop.
     * @return bool         True on success, false otherwise.
     */
    public function dropDatabase( string $name ): bool
    {
        $this->assertIdentifier( $name ) ;
        return $this->pdo?->exec( sprintf("DROP DATABASE IF EXISTS `%s`" , $name ) ) !== false;
    }

    /**
     * Drops a MySQL user if it exists.
     *
     * @param string $username  The username to drop.
     * @param string $host      The host (default: 'localhost').
     * @return bool             True on success, false otherwise.
     */
    public function dropUser( string $username , string $host = 'localhost' ): bool
    {
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;
        return $this->pdo?->exec( sprintf("DROP USER IF EXISTS '%s'@'%s'" , $username , $host ) ) !== false;
    }

    /**
     * Grants all privileges on a database to a user.
     *
     * @param string $username  The user to grant privileges to.
     * @param string $host      The user's host (usually 'localhost').
     * @param string $dbname    The database to grant access to.
     * @return bool             True on success, false otherwise.
     */
    public function grantPrivileges( string $username , string $host , string $dbname ): bool
    {
        $this->assertIdentifier ( $dbname   ) ;
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;
        $query = sprintf( "GRANT ALL PRIVILEGES ON `%s`.* TO '%s'@'%s'", $dbname , $username , $host ) ;
        return $this->pdo->exec( $query ) !== false;
    }

    /**
     * Checks whether a user has a specific privilege on a database or table.
     *
     * @param string      $username   MySQL username.
     * @param string      $privilege  Privilege to check (e.g. SELECT, INSERT, ALL PRIVILEGES).
     * @param string      $dbname     Database name.
     * @param string|null $table      Optional table name. If null, check DB-level privileges.
     * @param string      $host       Host (default: 'localhost').
     * @return bool                   True if the privilege is granted.
     */
    public function hasPrivilege
    (
        string $username,
        string $privilege,
        string $dbname,
        ?string $table = null,
        string $host = 'localhost'
    ): bool
    {
        $this->assertIdentifier ( $username ) ;
        $this->assertIdentifier ( $dbname   ) ;
        $this->assertHost       ( $host     ) ;

        if ($table !== null) {
            $this->assertIdentifier($table);
        }

        $privilege = strtoupper($privilege);
        $scope     = $table ? "{$dbname}.{$table}" : "{$dbname}.*";

        $grants = $this->listPrivileges($username, $host);

        // Vérification explicite (par scope précis ou global "ALL")
        foreach ([$scope, 'ALL'] as $key) {
            if (!isset($grants[$key])) {
                continue;
            }

            $privs = array_map('strtoupper', $grants[$key]);

            if (in_array('ALL PRIVILEGES', $privs, true) || in_array($privilege, $privs, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Flushes MySQL privileges.
     *
     * This reloads the grant tables to apply recent changes (like CREATE USER, GRANT, etc.).
     *
     * @return bool True on success, false otherwise.
     */
    public function flushPrivileges(): bool
    {
        return $this->pdo->exec( "FLUSH PRIVILEGES" ) !== false ;
    }

    /**
     * Retrieves all GRANT statements for a given user.
     *
     * @param string $username  The MySQL username.
     * @param string $host      The associated host (default: 'localhost').
     * @return array<int, string>  Array of GRANT statements (or empty if none/failure).
     */
    public function getGrants(string $username, string $host = 'localhost'): array
    {
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;

        $query = sprintf("SHOW GRANTS FOR '%s'@'%s'", $username , $host ) ;

        try
        {
            $stmt = $this->pdo?->query( $query );
            return $stmt ? $stmt->fetchAll( PDO::FETCH_COLUMN) : [] ;
        }
        catch ( PDOException $e )
        {
            return [] ; // Access denied or unknown user: return empty
        }
    }

    /**
     * Lists all tables in the current database.
     *
     * @return array<int, string>  Array of table names.
     */
    public function listCurrentTables(): array
    {
        $query = "SHOW TABLES";

        try
        {
            $stmt = $this->pdo?->query($query);
            return $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
        }
        catch ( PDOException $e )
        {
            return [];
        }
    }

    /**
     * Lists parsed privileges granted to a user.
     *
     * @param string $username  The MySQL user.
     * @param string $host      The associated host (default: 'localhost').
     * @return array<string, array<string>>  [ 'database.table' => [privileges...] ]
     *
     * @example
     * ```php
     * $list = $model->listPrivileges('user1');
     *
     * foreach ( $list as $scope => $rights)
     * {
     *     echo "Privileges on {$scope}:\n - " . implode(', ', $rights) . PHP_EOL ;
     * }
     *
     * // Privileges on mydb.*:
     * // - SELECT, INSERT, UPDATE
     *
     * // Privileges on mydb.products:
     * // - SELECT
     *
     * // Privileges on ALL:
     * // - USAGE
     * ```
     */
    public function listPrivileges(string $username, string $host = 'localhost') :array
    {
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;

        $grants  = $this->getGrants( $username , $host ) ;
        $results = [] ;

        foreach ( $grants as $grant )
        {
            if (preg_match('/^GRANT (.+) ON (.+) TO /i', $grant, $matches))
            {
                $privileges = array_map('trim', explode(',', $matches[1]));
                $target     = trim( $matches[2] , '`' ) ;

                // Normalisation : *.* → ALL, otherwise use db.table
                $key = $target === '*.*' ? 'ALL' : str_replace('`.`', '.', $target);
                $results[$key] = $privileges ;
            }
        }

        return $results;
    }

    /**
     * Renames an existing MySQL user.
     *
     * @param string $fromUser  Current username.
     * @param string $fromHost  Current host.
     * @param string $toUser    New username.
     * @param string $toHost    New host.
     * @return bool             True on success, false otherwise.
     *
     * @example
     * ```php
     * $grants = $model->getGrants('user1', 'localhost');
     *
     * foreach ( $grants as $grant )
     * {
     *     echo $grant . PHP_EOL;
     * }
     * ```
     */
    public function renameUser( string $fromUser , string $fromHost , string $toUser , string $toHost ): bool
    {
        $this->assertIdentifier ( $fromUser ) ;
        $this->assertIdentifier ( $toUser   ) ;
        $this->assertHost       ( $fromHost ) ;
        $this->assertHost       ( $toHost   ) ;

        $query = sprintf( "RENAME USER '%s'@'%s' TO '%s'@'%s'", $fromUser, $fromHost, $toUser, $toHost );

        return $this->pdo->exec($query) !== false;
    }

    /**
     * Revokes specific privileges from a user on a database or table.
     *
     * @param string              $privileges  Comma-separated privileges (e.g. 'SELECT, INSERT').
     * @param string              $dbname      Database name.
     * @param string              $username    MySQL user.
     * @param string              $host        Host (default: 'localhost').
     * @param string|null         $table       Optional table name (null = whole DB).
     * @return bool                            True if the statement was executed successfully.
     *
     * @example
     * 1. Remove all modification rights:
     * ```php
     * $model->revokePrivilege('INSERT, UPDATE, DELETE', 'mydb', 'user1');
     * ```
     *
     * 2. Only remove SELECT from a specific table
     * ```php
     * $model->revokePrivilege('SELECT', 'mydb', 'user1', 'localhost', 'products');
     * ```
     */
    public function revokePrivilege
    (
        string  $privileges ,
        string  $dbname ,
        string  $username ,
        string  $host  = 'localhost' ,
        ?string $table = null
    ): bool
    {
        $this->assertIdentifier ( $dbname   ) ;
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;

        if ( $table !== null )
        {
            $this->assertIdentifier( $table ) ;
        }

        $object = $table ? sprintf('`%s`.`%s`', $dbname, $table) : sprintf('`%s`.*', $dbname);

        $query = sprintf
        (
            'REVOKE %s ON %s FROM \'%s\'@\'%s\'',
            $privileges,
            $object,
            $username,
            $host
        );

        return $this->pdo->exec( $query ) !== false ;
    }

    /**
     * Revokes all privileges from a user on a specific database.
     *
     * @param string $username  The user to revoke privileges from.
     * @param string $host      The user's host (usually 'localhost').
     * @param string $dbname    The database to revoke access from.
     * @return bool             True on success, false otherwise.
     */
    public function revokePrivileges(string $username, string $host, string $dbname): bool
    {
        $this->assertIdentifier ( $dbname   ) ;
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;

        $query = sprintf( "REVOKE ALL PRIVILEGES ON `%s`.* FROM '%s'@'%s'" , $dbname , $username , $host ) ;

        return $this->pdo->exec( $query ) !== false ;
    }

    /**
     * Dumps current users and databases into a structured array.
     *
     * @return array<string, mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function toArray(): array
    {
        return
        [
            'databases' => $this->fetchColumnArray("SHOW DATABASES"),
            'users'     => $this->fetch("SELECT User, Host FROM mysql.user" ),
        ];
    }

    /**
     * Checks if a MySQL user exists.
     *
     * @param string $username  Username to check.
     * @param string $host      Host (default: 'localhost').
     * @return bool              True if the user exists.
     */
    public function userExists( string $username , string $host = 'localhost' ): bool
    {
        $this->assertIdentifier($username);
        $this->assertHost($host);

        $query = "SELECT 1 FROM mysql.user WHERE User = :user AND Host = :host";
        $stmt  = $this->pdo?->prepare($query);

        if (! $stmt )
        {
            return false ;
        }

        $this->bindValues( $stmt ,
        [
            'user' => $username ,
            'host' => $host     ,
        ]);

        return $stmt->execute() && $stmt->fetchColumn() !== false;
    }

    // ------------------- Protected

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