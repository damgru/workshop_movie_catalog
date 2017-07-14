<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:56
 */

namespace Domain;


use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

class Movie extends AggregateRoot implements \JsonSerializable
{
    /** @var  Uuid */
    private $uuid;
    /** @var  string */
    private $name;
    /** @var  string */
    private $img;

    /**
     * @param $uuid
     * @param $name
     * @param $img
     * @return Movie
     */
    public static function new($uuid, $name, $img)
    {
        $self = new self();
        $self->uuid = $uuid;
        $self->name = $name;
        $self->img = $img;
        return $self;
    }


    /**
     * @return Uuid
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    function jsonSerialize()
    {
        return [
            'id' => $this->getUuid()->toString(),
            'name' => $this->getName(),
            'img' => $this->getImg()
        ];
    }

    /**
     * @return string representation of the unique identifier of the aggregate root
     */
    protected function aggregateId()
    {
        return $this->getUuid()->toString();
    }
}