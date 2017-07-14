<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 08:43
 */

namespace Application\Command;


use Application\SimpleCommand;

class AddMovie extends SimpleCommand
{
    private $uuid;
    private $name;
}