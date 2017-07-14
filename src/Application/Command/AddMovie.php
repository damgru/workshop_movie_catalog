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

/**
 * Class AddMovie
 * @package Application\Command
 */
class AddMovie extends SimpleCommand
{
    /**
     * @var Uuid
     */
    private $uuid;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $img;
    /**
     * @var string
     */
    private $url;

    /**
     * AddMovie constructor.
     * @param Uuid $uuid
     * @param string $name
     * @param string $img
     * @param string $url
     */
    public function __construct(Uuid $uuid, string $name, string $img, string $url)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->img = $img;
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }



}