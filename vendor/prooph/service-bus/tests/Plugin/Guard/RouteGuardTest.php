<?php
/**
 * This file is part of the prooph/service-bus.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ProophTest\ServiceBus\Plugin\Guard;

use PHPUnit_Framework_TestCase as TestCase;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\ListenerHandler;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Guard\AuthorizationService;
use Prooph\ServiceBus\Plugin\Guard\RouteGuard;

/**
 * Class RouteGuardTest
 * @package ProophTest\ServiceBus\Plugin\Guard
 */
final class RouteGuardTest extends TestCase
{
    /**
     * @test
     */
    public function it_attaches_to_action_event_emitter()
    {
        $listenerHandler = $this->prophesize(ListenerHandler::class);

        $authorizationService = $this->prophesize(AuthorizationService::class);

        $routeGuard = new RouteGuard($authorizationService->reveal());

        $actionEventEmitter = $this->prophesize(ActionEventEmitter::class);
        $actionEventEmitter
            ->attachListener(MessageBus::EVENT_ROUTE, [$routeGuard, 'onRoute'], 1000)
            ->willReturn($listenerHandler->reveal());

        $routeGuard->attach($actionEventEmitter->reveal());
    }

    /**
     * @test
     */
    public function it_allows_when_authorization_service_grants_access()
    {
        $authorizationService = $this->prophesize(AuthorizationService::class);
        $authorizationService->isGranted('test_event', new \stdClass())->willReturn(true);

        $actionEvent = $this->prophesize(ActionEvent::class);
        $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_NAME)->willReturn('test_event');
        $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE)->willReturn(new \stdClass());

        $routeGuard = new RouteGuard($authorizationService->reveal());

        $this->assertNull($routeGuard->onRoute($actionEvent->reveal()));
    }

    /**
     * @test
     * @expectedException \Prooph\ServiceBus\Plugin\Guard\UnauthorizedException
     * @expectedExceptionMessage You are not authorized to access this resource
     */
    public function it_stops_propagation_and_throws_unauthorizedexception_when_authorization_service_denies_access()
    {
        $authorizationService = $this->prophesize(AuthorizationService::class);
        $authorizationService->isGranted('test_event', new \stdClass())->willReturn(false);

        $actionEvent = $this->prophesize(ActionEvent::class);
        $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_NAME)->willReturn('test_event');
        $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE)->willReturn(new \stdClass());
        $actionEvent->stopPropagation(true)->willReturn(null);

        $routeGuard = new RouteGuard($authorizationService->reveal());

        $routeGuard->onRoute($actionEvent->reveal());
    }

    /**
     * @test
     * @expectedException \Prooph\ServiceBus\Plugin\Guard\UnauthorizedException
     * @expectedExceptionMessage You are not authorized to access the resource "test_event"
     */
    public function it_stops_propagation_and_throws_unauthorizedexception_when_authorization_service_denies_access_and_exposed_message_name()
    {
        $authorizationService = $this->prophesize(AuthorizationService::class);
        $authorizationService->isGranted('test_event', new \stdClass())->willReturn(false);

        $actionEvent = $this->prophesize(ActionEvent::class);
        $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_NAME)->willReturn('test_event');
        $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE)->willReturn(new \stdClass());
        $actionEvent->stopPropagation(true)->willReturn(null);

        $routeGuard = new RouteGuard($authorizationService->reveal(), true);

        $routeGuard->onRoute($actionEvent->reveal());
    }
}
