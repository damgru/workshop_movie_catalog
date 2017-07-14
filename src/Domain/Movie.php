<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:56
 */

namespace Domain;


use Domain\Event\MovieAdded;
use Domain\Event\MovieDeleted;
use Domain\Event\MovieWatched;
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
    /** @var  string */
    private $link;

    private $amount = 5.00;

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
    private $currency = 'PLN';

    /**
     * @param $uuid
     * @param $name
     * @param $img
     * @return Movie
     */
    public static function new($uuid, $name, $img, $url)
    {
        $self = new self();
        $self->uuid = $uuid;
        $self->name = $name;
        $self->img = $img;
        $self->link = $url;
        $self->recordThat(MovieAdded::from($uuid, $name, $url, $img));
        return $self;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    public function watchMovie()
    {
        $this->recordThat(MovieWatched::from($this->getUuid(), $this->getName()));
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
            'img' => $this->getImg(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency()
        ];
    }

    /**
     * @return string representation of the unique identifier of the aggregate root
     */
    protected function aggregateId()
    {
        return $this->getUuid();
    }

    public function whenMovieAdded(MovieAdded $e)
    {
        $this->uuid = $e->getUuid();
        $this->name = $e->getName();
        $this->link = $e->getUrl();
        $this->img = $e->getImg();
    }

    public function whenMovieDeleted(MovieDeleted $e)
    {

    }

    public function whenMovieWatched(MovieWatched $e)
    {

    }
}