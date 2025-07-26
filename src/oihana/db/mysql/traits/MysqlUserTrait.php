<?php

namespace oihana\db\mysql\traits;

use oihana\models\pdo\PDOTrait;
use PDOStatement;

trait MysqlUserTrait
{
    use MysqlAssertionsTrait ,
        PDOTrait ;

    /**
     * Creates a new MySQL user with a password.
     *
     * @param string $username  The username to create.
     * @param string $host      The host (default: 'localhost').
     * @param string $password  The password for the user.
     * @return bool             True on success, false otherwise.
     */
    public function createUser(string $username , string $host = 'localhost' , string $password = '' ): bool
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
     * Renames an existing MySQL user.
     *
     * @param string $fromUser  Current username.
     * @param string $fromHost  Current host.
     * @param string $toUser    New username.
     * @param string $toHost    New host.
     * @return bool             True on success, false otherwise.
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
     * Drops a MySQL user if it exists.
     *
     * @param string $username  The username to drop.
     * @param string $host      The host (default: 'localhost').
     * @return bool             True on success, false otherwise.
     */
    public function userDrop( string $username , string $host = 'localhost' ): bool
    {
        $this->assertIdentifier ( $username ) ;
        $this->assertHost       ( $host     ) ;
        return $this->pdo?->exec( sprintf("DROP USER IF EXISTS '%s'@'%s'" , $username , $host ) ) !== false;
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