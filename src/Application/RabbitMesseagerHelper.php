<?php
/**
 * Created by PhpStorm.
 * User: Cainon
 * Date: 14.07.2017
 * Time: 11:30
 */

namespace Application;


class RabbitMesseagerHelper
{
    public static function createMessage($className, $eventId, array $data)
    {
        $response = [
            'event' => self::createEventNameFromClassName($className),
            'event_id' => $eventId,
            'created' => date("Y-m-d H:i:s"),
            'version' => 1,
            'data' => $data
        ];

        return json_encode($response);
    }

    public static function createEventNameFromClassName($className)
    {
        return str_replace('\\', '_', $className);
    }
}