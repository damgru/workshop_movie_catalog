<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 12.07.2017
 * Time: 14:27
 */

namespace Application;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitSender
{
    public static function sendMessage($messageBody, $exchange = 'mk-router')
    {
        $connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASS);
        $channel = $connection->channel();

        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);

        $channel->close();
        $connection->close();
    }
}