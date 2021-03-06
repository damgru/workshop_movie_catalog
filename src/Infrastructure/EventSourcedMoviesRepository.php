<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 09:06
 */

namespace Infrastructure;


use Domain\Movie;
use Domain\MoviesRepository;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Rhumsaa\Uuid\Uuid;

class EventSourcedMoviesRepository implements MoviesRepository
{
    private $ar;

    /**
     * EventSourcedMoviesRepository constructor.
     * @param $ar
     */
    public function __construct(AggregateRepository $ar)
    {
        $this->ar = $ar;
    }

    public function getMovie(Uuid $uuid) : Movie
    {
        return $this->ar->getAggregateRoot($uuid->toString());
    }

    public function saveMovie(Movie $movie)
    {
        $this->ar->addAggregateRoot($movie);
    }
}