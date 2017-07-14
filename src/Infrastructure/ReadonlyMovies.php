<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 10:15
 */

namespace Infrastructure;


use Doctrine\DBAL\Connection;
use Domain\Event\MovieAdded;
use Domain\Event\MovieDeleted;
use Domain\MovieEvent;
use PDO;

class ReadonlyMovies
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * ReadonlyMovies constructor.
     * @param Connection $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getMovies()
    {
        $sql = <<<SQL
SELECT * FROM movie_catalog.readonly_movies;
SQL;

        return $this->connection->fetchAll($sql);
    }

    public function whenMovieAdded(MovieAdded $e)
    {
        $this->connection->executeQuery(
            'INSERT 
                INTO movie_catalog.readonly_movies (uuid, name, img, url, createdAt) 
                VALUES (?,?,?,?,?)',
            [
                (string)$e->getUuid(), $e->getName(), $e->getImg(), $e->getUrl(), $e->getDate()
            ]);
    }

    public function whenMovieDeleted(MovieDeleted $e)
    {
        $this->connection->executeQuery(
            'DELETE FROM movie_catalog.readonly_movies 
                WHERE uuid = ?',
            [
                (string)$e->getUuid()
            ]);
    }

}