<?php

namespace oihana\db\mysql\traits;

use oihana\models\pdo\PDOTrait;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Provides methods to manage MySQL users using PDO.
 * Includes operations for creating, renaming, deleting, and checking the existence of users.
 *
 * Requires a connected PDO instance and uses `MysqlAssertionsTrait` for input validation.
 *
 * @package oihana\db\mysql\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait MysqlUserTrait
{
    use MysqlAssertionsTrait ,
        PDOTrait ;

    /**
     * Creates a new MySQL user with the given username, host, and password.
     * If the user already exists, the operation has no effect.
     *
     * @param string $username The username to create.
     * @param string $host     The host from which the user connects (default: 'localhost').
     * @param string $password The password for the user.
     *
     * @return bool True on success, false otherwise.
     */
    public function createUser( string $username , string $host = 'localhost' , string $password = '' ): bool
    {
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;

        $query = sprintf("CREATE USER IF NOT EXISTS '%s'@'%s' IDENTIFIED BY :password", $username , $host ) ;
        $statement = $this->pdo?->prepare( $query ) ;

        if ( $statement instanceof PDOStatement )
        {
            $this->bindValues( $statement , [ 'password' => $password ] ) ;
            return $statement->execute() ;
        }

        return false;
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
     * Returns a list of MySQL users with their associated hosts.
     *
     * @param string|null $like      Optional SQL pattern to filter users (e.g. 'wp%').
     * @param bool        $grouped   Whether to group hosts under each username.
     * @param bool        $throwable Indicates if the method is throwable.
     *
     * @return array  If grouped, returns array<string, string[]> (user => [hosts]).
     *               Otherwise, returns array<int, array{user: string, host: string}>.
     */
    public function listUsers( ?string $like = null, bool $grouped = false , bool $throwable = false ): array
    {
        $params = [];

        $query = "SELECT User AS user, Host AS host FROM mysql.user ORDER BY User, Host";

        if ( isset($like) )
        {
            $query .= " WHERE User LIKE :like";
            $params['like'] = $like;
        }

        $query .= " ORDER BY User, Host" ;

        try
        {

            $statement = $this->pdo?->prepare( $query ) ;

            if ( !$statement )
            {
                return [] ;
            }

            $statement->execute( $params );

            $results   = $statement->fetchAll( PDO::FETCH_ASSOC ) ?: [] ;
            $statement = null ;

            if ( !$grouped )
            {
                return $results;
            }

            $groups = [] ;

            foreach ( $results as $row )
            {
                $user = $row[ 'user' ] ;
                $host = $row[ 'host' ] ;
                $groups[ $user ][] = $host ;
            }

            return $groups ;
        }
        catch ( PDOException $exception )
        {
            if( $throwable )
            {
                throw $exception ;
            }
            return [] ;
        }
    }

    /**
     * Renames an existing MySQL user.
     *
     * @param string $fromUser Current username.
     * @param string $fromHost Current host.
     * @param string $toUser   New username.
     * @param string $toHost   New host.
     *
     * @return bool True if the rename operation was successful, false otherwise.
     */
    public function renameUser( string $fromUser , string $fromHost , string $toUser , string $toHost ): bool
    {
        $this->assertIdentifier ( $fromUser ) ;
        $this->assertIdentifier ( $toUser   ) ;
        $this->assertHost       ( $fromHost ) ;
        $this->assertHost       ( $toHost   ) ;

        $query = sprintf( "RENAME USER '%s'@'%s' TO '%s'@'%s'" , $fromUser , $fromHost , $toUser , $toHost );

        return $this->pdo->exec( $query ) !== false ;
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
        $this->assertIdentifier( $username ) ;
        $this->assertHost( $host );

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

        return $stmt->execute() && $stmt->fetchColumn() !== false ;
    }
}