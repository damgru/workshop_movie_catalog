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
    public function getMovie(Uuid $uuid);
    public function saveMovie(Movie $movie);
}