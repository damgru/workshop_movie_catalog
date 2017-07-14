<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 11.07.2017
 * Time: 15:43
 */

namespace Application;


use Prooph\Common\Messaging\Command;

class SimpleCommand extends Command
{
    /**
     * SimpleCommand constructor.
     */
    public function __construct()
    {
        $this->init();
    }


    /**
     * Return message payload as array
     *
     * The payload should only contain scalar types and sub arrays.
     * The payload is normally passed to json_encode to persist the message or
     * push it into a message queue.
     *
     * @return array
     */
    public function payload()
    {
        return get_object_vars($this);
    }

    /**
     * This method is called when message is instantiated named constructor fromArray
     *
     * @param array $payload
     * @return void
     */
    protected function setPayload(array $payload)
    {
        foreach ($payload as $key => $value) {
            $this->{$key} = $value;
        }
    }
}