<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 09:08
 */

namespace Domain\Event;


use Domain\MovieEvent;

class MovieWatched extends MovieEvent
{
    public static function from($uuid, $name)
    {
        return MovieEvent::new([
            'name' => $name,
            'uuid' => (string)$uuid
        ]);
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