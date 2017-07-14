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
class AddCategory extends SimpleCommand
{
    /**
     * @var Uuid
     */
    protected $uuid;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $urlName;

    /**
     * AddMovie constructor.
     * @param Uuid $uuid
     * @param string $name
     * @param string $urlName
     */
    public function __construct(Uuid $uuid, string $name, string $urlName)
    {
        parent::__construct();
        $this->uuid = $uuid;
        $this->name = $name;
        $this->urlName = $urlName;
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
     * @return string
     */
    public function getUrlName(): string
    {
        return $this->urlName;
    }




}