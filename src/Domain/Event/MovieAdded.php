<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:47
 */

namespace Domain\Event;


use Domain\MovieEvent;
use Prooph\EventSourcing\AggregateChanged;

class MovieAdded extends MovieEvent
{
    public static function from($uuid, $name, $img, $url)
    {
        return MovieEvent::new([
            'uuid' => $uuid,
            'name' => $name,
            'url' => $img,
            'img' => $url
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

    public function getUrl()
    {
        return $this->payload['url'];
    }

    public function getImg()
    {
        return $this->payload['img'];
    }
}