<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:47
 */

namespace Domain\Event;


use Prooph\EventSourcing\AggregateChanged;

class MovieAdded extends AggregateChanged
{
    public static function from($uuid, $name)
    {
        return self::occur(
            (string)$uuid,
            [
                'name'=>$name
            ]
        );
    }

}