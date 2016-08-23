<?php

namespace Oro\Bundle\FeatureToggleBundle\Tests\Unit\EventListener;

use Oro\Bundle\FeatureToggleBundle\Checker\FeatureChecker;
use Oro\Bundle\FeatureToggleBundle\EventListener\RequestListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestListener
     */
    private $listener;

    /**
     * @var FeatureChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $featureChecker;

    public function setUp()
    {
        $this->featureChecker = $this->getMockBuilder(FeatureChecker::class);
        $this->listener = new RequestListener($this->featureChecker);
    }

    public function testWhenRouteFeatureDisabled()
    {
        $this->featureChecker
            ->expects($this->once())
            ->method('isResourceEnabled')
            ->with('oro_login', 'route', 'website')
            ->willReturn(false);

        $request = $this->getMock(Request::class);
        $request->method('get')->with('_route')->willReturn('oro_login');
        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder(GetResponseEvent::class)->disableOriginalConstructor()->getMock();
        $event->method('getRequest')->willReturn($request);
        
        $event->expects($this->once())->method('setResponse');
        $this->listener->onRequest($event);
    }

    public function testWhenRouteFeatureEnabled()
    {
        $this->featureChecker
            ->expects($this->once())
            ->method('isResourceEnabled')
            ->with('oro_login', 'route', 'website')
            ->willReturn(true);

        $request = $this->getMock(Request::class);
        $request->method('get')->with('_route')->willReturn('oro_login');
        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMockBuilder(GetResponseEvent::class)->disableOriginalConstructor()->getMock();
        $event->method('getRequest')->willReturn($request);
        
        $event->expects($this->never())->method('setResponse');
        $this->listener->onRequest($event);
    }
}
