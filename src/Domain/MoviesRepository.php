<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:55
 */

namespace Domain;


use Money\Money;
use Rhumsaa\Uuid\Uuid;

interface MoviesRepository
{
    public function GetMovies();
    public function GetMovie(Uuid $uuid);
    public function AddMovie(Movie $movie);
    public function DeleteMovieByUuid(Uuid $uuid);
}