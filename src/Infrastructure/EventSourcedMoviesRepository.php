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
use Rhumsaa\Uuid\Uuid;

class EventSourcedMoviesRepository implements MoviesRepository
{

    public function GetMovies()
    {
        // TODO: Implement GetMovies() method.
    }

    public function GetMovie(Uuid $uuid)
    {
        // TODO: Implement GetMovie() method.
    }

    public function AddMovie(Movie $movie)
    {
        // TODO: Implement AddMovie() method.
    }

    public function DeleteMovieByUuid(Uuid $uuid)
    {
        // TODO: Implement DeleteMovieByUuid() method.
    }
}