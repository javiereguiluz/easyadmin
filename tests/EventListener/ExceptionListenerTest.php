<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\EventListener\ExceptionListener;
use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityNotFoundException as EasyEntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExceptionListenerTest extends TestCase
{
    private function getTwig()
    {
        $twig = $this->getMockBuilder('\Twig_Environment')->disableOriginalConstructor()->getMock();
        $twig->method('render')->willReturn('template content');

        return $twig;
    }

    private function getEventExceptionThatShouldBeCalledOnce($exception)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getException')->willReturn($exception);
        $event->method('getRequest')->willReturn(new Request());
        $event->method('getKernel')->willReturn(new TestKernel());
        $event->expects($this->once())->method('setResponse');

        return $event;
    }

    public function testCatchBaseExceptions()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $exception = new EasyEntityNotFoundException([
            'entity_name' => 'Test',
            'entity_id_name' => 'Test key',
            'entity_id_value' => 2,
        ]);
        $event = $this->getEventExceptionThatShouldBeCalledOnce($exception);
        $twig = $this->getTwig();

        $listener = new ExceptionListener($twig, []);
        $listener->onKernelException($event);
    }

    private function getEventExceptionThatShouldNotBeCalled($exception)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getException')->willReturn($exception);
        $event->method('getRequest')->willReturn(new Request());
        $event->expects($this->never())->method('setResponse');

        return $event;
    }

    public function testShouldNotCatchExceptionsWithSameName()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $exception = new EntityNotFoundException();
        $event = $this->getEventExceptionThatShouldNotBeCalled($exception);
        $twig = $this->getTwig();

        $listener = new ExceptionListener($twig, []);
        $listener->onKernelException($event);
    }
}

class EntityNotFoundException extends \Exception
{
}

class TestKernel implements HttpKernelInterface
{
    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        return new Response('foo');
    }
}
