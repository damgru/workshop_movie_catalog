<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:43
 */

namespace Application\Command;


use Application\SimpleCommand;
use Badcow\LoremIpsum\Generator;
use Rhumsaa\Uuid\Uuid;

/**
 * Class AddMovie
 * @package Application\Command
 */
class AddRandomMovie extends SimpleCommand
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

    public function __construct()
    {
        parent::__construct();
        $this->uuid = Uuid::uuid4();
        $this->name = 'sadf';
        $this->img = 'http://lorempixel.com/400/200/';
        $this->url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
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