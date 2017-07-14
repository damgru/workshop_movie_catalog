<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 09:08
 */

namespace Domain\Event;


use Prooph\EventSourcing\AggregateChanged;

class MovieDeleted extends AggregateChanged
{
    const NAME = 'MOVIE_DELETED';

    public static function from($uuid, $name)
    {
        return self::occur(
            (string)$uuid,
            [
                'name'=>$name,
                'uuid'=>$name
            ]
        );
    }

    public function getName()
    {
        return $this->payload['name'];
    }

    public function getUuid()
    {
        return $this->payload['uuid'];
    }
}