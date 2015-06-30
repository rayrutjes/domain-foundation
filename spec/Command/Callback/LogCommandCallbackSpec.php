<?php

namespace spec\RayRutjes\DomainFoundation\Command\Callback;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use RayRutjes\DomainFoundation\Command\Command;

class LogCommandCallbackSpec extends ObjectBehavior
{
    public function let(Command $command, LoggerInterface $logger)
    {
        $this->beConstructedWith($command, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Callback\LogCommandCallback');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Callback\CommandCallback');
    }

    public function it_logs_an_info_message_on_success($logger)
    {
        $logger->info(Argument::cetera())->shouldBeCalled();
        $this->onSuccess();
    }

    public function it_logs_a_warning_message_on_failure($logger, \Exception $cause)
    {
        $logger->warning(Argument::cetera())->shouldBeCalled();
        $this->onFailure($cause);
    }
}
