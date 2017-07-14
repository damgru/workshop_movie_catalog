<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 10:15
 */

namespace Infrastructure;


use Doctrine\DBAL\Connection;
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

}