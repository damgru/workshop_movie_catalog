<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:45
 */

namespace Application\Command;


class WatchMovie
{
    private $movieUuid;
    private $clientUuid;

    /**
     * @return mixed
     */
    public function getMovieUuid()
    {
        return $this->movieUuid;
    }

    /**
     * @return mixed
     */
    public function getClientUuid()
    {
        return $this->clientUuid;
    }

    /**
     * WatchMovie constructor.
     * @param $movieUuid
     * @param $clientUuid
     */
    public function __construct($movieUuid, $clientUuid)
    {
        $this->movieUuid = $movieUuid;
        $this->clientUuid = $clientUuid;
    }


}