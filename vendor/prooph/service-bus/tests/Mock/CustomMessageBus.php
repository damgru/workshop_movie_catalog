<?php
/**
 * This file is part of the prooph/service-bus.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ProophTest\ServiceBus\Mock;

use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;

/**
 * Class CustomMessageBus
 * @package ProophTest\ServiceBus\Mock
 */
final class CustomMessageBus extends MessageBus
{
    private $actionEvent;

    /**
     * @param mixed $message
     */
    public function dispatch($message)
    {
        $this->initialize($message, $this->getActionEvent());
    }

    public function setActionEvent(ActionEvent $event)
    {
        $this->actionEvent = $event;
    }

    public function getActionEvent()
    {
        if (null === $this->actionEvent) {
            $this->actionEvent = $this->getActionEventEmitter()->getNewActionEvent();
        }

        return $this->actionEvent;
    }
}
