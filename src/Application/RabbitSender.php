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
    public static function sendMessage($messageBody, $exchange = 'router', $queue = 'msgs')
    {
        $connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASS);
        $channel = $connection->channel();

        /*
        name: $queue
        passive: false
        durable: true // the queue will survive server restarts
        exclusive: false // the queue can be accessed in other channels
        auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $channel->queue_declare($queue, false, true, false, false);

        /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */
        $channel->exchange_declare($exchange, 'direct', false, true, false);

        $channel->queue_bind($queue, $exchange);

        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);

        $channel->close();
        $connection->close();
    }
}