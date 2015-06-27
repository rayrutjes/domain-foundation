<?php

namespace spec\RayRutjes\DomainFoundation\Command\Callback;

use Monolog\Logger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Command\Command;

class LogCommandCallbackSpec extends ObjectBehavior
{
    public function let(Command $command, Logger $logger)
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
        $logger->addInfo(Argument::cetera())->shouldBeCalled();
        $this->onSuccess();
    }

    public function it_logs_a_warning_message_on_failure($logger, \Exception $cause)
    {
        $logger->addWarning(Argument::cetera())->shouldBeCalled();
        $this->onFailure($cause);
    }
}
