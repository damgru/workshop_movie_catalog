<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:43
 */

namespace Application\Command;


use Application\SimpleCommand;
use Rhumsaa\Uuid\Uuid;

class DeleteMovie extends SimpleCommand
{
    private $uuid;

    /**
     * DeleteMovie constructor.
     * @param $uuid
     */
    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }


    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }


}