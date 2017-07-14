<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 09:39
 */

namespace Domain;


use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

class MovieEvent extends AggregateChanged
{
    /** @var string */
    private $date;
    private $version = '1';
    private $eventId;
    private $data;

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    public static function new($data, $date = null, $eventId = null)
    {
        $self = new self();
        $self->date = empty($date) ? date('Y-m-d H:i:s') : $date;
        $self->version = '1';
        $self->eventId = empty($eventId) ? Uuid::uuid4()->toString() : $eventId;
        $self->data = $data;
        return $self;
    }

    public function getData() {
        return $this->date;
    }
}