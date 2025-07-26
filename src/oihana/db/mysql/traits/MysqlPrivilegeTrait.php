<?php

namespace oihana\db\mysql\traits;

use oihana\models\pdo\PDOTrait;
use PDO;
use PDOException;

trait MysqlPrivilegeTrait
{
    use MysqlAssertionsTrait ,
        PDOTrait ;

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

        if ( $table !== null )
        {
            $this->assertIdentifier($table);
        }

        $privilege = strtoupper($privilege);
        $scope     = $table ? "{$dbname}.{$table}" : "{$dbname}.*";

        $grants = $this->listPrivileges($username, $host);

        foreach ( [ $scope , 'ALL' ] as $key )
        {
            if (!isset($grants[$key])) {
                continue;
            }

            $privileges = array_map('strtoupper', $grants[$key]);

            if ( in_array('ALL PRIVILEGES' , $privileges , true ) || in_array( $privilege , $privileges , true ) )
            {
                return true;
            }
        }

        return false;
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
            $statements = $this->pdo?->query( $query );
            return $statements ? $statements->fetchAll( PDO::FETCH_COLUMN ) : [] ;
        }
        catch ( PDOException $e )
        {
            return [] ; // Access denied or unknown user: return empty
        }
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
}