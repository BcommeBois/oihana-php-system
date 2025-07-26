<?php

namespace oihana\db\mysql\traits;

use oihana\models\pdo\PDOTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait MysqlDatabaseTrait
{
    use MysqlAssertionsTrait ,
        PDOTrait ;

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
     * Checks if a MySQL database exists.
     *
     * @param string $name  Database name.
     * @return bool         True if the database exists.
     */
    public function databaseExists( string $name ): bool
    {
        $this->assertIdentifier( $name );

        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :name" ;
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
     * Returns the default character set and collation of a database.
     *
     * @param string $dbname
     * @return array{Charset: string, Collation: string}|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDatabaseCharset(string $dbname): ?array
    {
        $this->assertIdentifier( $dbname ) ;

        $query = "SELECT DEFAULT_CHARACTER_SET_NAME AS Charset, DEFAULT_COLLATION_NAME AS Collation 
              FROM INFORMATION_SCHEMA.SCHEMATA 
              WHERE SCHEMA_NAME = :dbname" ;

        $result = $this->fetch($query, ['dbname' => $dbname]);

        return is_array($result) ? $result : null;
    }

    /**
     * Returns the size of a database in bytes.
     *
     * @param string $dbname
     * @return int Size in bytes.
     */
    public function getDatabaseSize(string $dbname): int
    {
        $this->assertIdentifier( $dbname ) ;

        $query = "SELECT SUM(DATA_LENGTH + INDEX_LENGTH) AS size FROM information_schema.TABLES WHERE TABLE_SCHEMA = :dbname";

        $result = $this->fetchColumn( $query , [ 'dbname' => $dbname ] ) ;

        return (int) $result;
    }

    /**
     * Lists all available databases.
     *
     * @param bool $excludeSystem Exclude system databases like 'information_schema', 'mysql', etc.
     * @return array<int, string> List of database names.
     */
    public function listDatabases( bool $excludeSystem = true ): array
    {
        $databases = $this->fetchColumnArray("SHOW DATABASES" ) ;

        if ( $excludeSystem )
        {
            $databases = array_filter($databases, fn( $db ) => !in_array( $db,
            [
                'information_schema' ,
                'mysql' ,
                'performance_schema' ,
                'sys'
            ]
            , true ) ) ;
        }

        return array_values( $databases ) ;
    }

    /**
     * Optimizes all tables in a database.
     *
     * @param string $dbname
     * @return bool True if all tables optimized successfully.
     */
    public function optimizeDatabase(string $dbname): bool
    {
        $this->assertIdentifier( $dbname ) ;

        $tables = $this->fetchColumnArray("SHOW TABLES FROM `$dbname`" ) ;

        foreach ( $tables as $table )
        {
            $query = "OPTIMIZE TABLE `$dbname`.`$table`" ;
            if ( $this->pdo->exec($query) === false )
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Repairs all tables in a database.
     *
     * @param string $dbname
     * @return bool True if all tables repaired successfully.
     */
    public function repairDatabase( string $dbname ): bool
    {
        $this->assertIdentifier( $dbname ) ;

        $tables = $this->fetchColumnArray("SHOW TABLES FROM `$dbname`" ) ;

        foreach ( $tables as $table )
        {
            $query = "REPAIR TABLE `$dbname`.`$table`" ;
            if ( false === $this->pdo->exec( $query ) )
            {
                return false;
            }
        }

        return true;
    }
}