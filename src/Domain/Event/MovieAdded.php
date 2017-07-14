<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:47
 */

namespace Domain\Event;


use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

class MovieAdded extends AggregateChanged
{
    const NAME = 'MOVIE_ADDED';

    /** @var string */
    protected $date;
    protected $version = '1';
    protected $eventId;
    protected $data;

    public static function from($uuid, $name, $url, $img)
    {
        return self::new([
            'uuid' => (string)$uuid,
            'name' => (string)$name,
            'url' => (string)$url,
            'img' => (string)$img,
        ]);
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->payload['date'];
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->payload['version'];
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {
        return $this->payload['event_id'];
    }

    public static function new($data, $date = null, $eventId = null)
    {
        $self = self::occur($data['uuid'],[
            'date' => empty($date) ? date('Y-m-d H:i:s') : $date,
            'version' => '1',
            'event_id' => empty($eventId) ? Uuid::uuid4()->toString() : $eventId,
            'data' => $data
        ]);
        return $self;
    }

    public function getData() {
        return $this->payload['data'];
    }

    public function getName()
    {
        return $this->payload['data']['name'];
    }

    public function getUuid()
    {
        return $this->payload['data']['uuid'];
    }

    public function getUrl()
    {
        return $this->payload['data']['url'];
    }

    public function getImg()
    {
        return $this->payload['data']['img'];
    }
}