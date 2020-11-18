<?php

namespace Cjm\PhpSpec\GithubActions;

use PhpSpec\Event\ExampleEvent;
use PhpSpec\Event\SuiteEvent;
use PhpSpec\IO\IO;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FailedExamplesFormatter implements EventSubscriberInterface
{
    /**
     * @var IO
     */
    private $io;

    /**
     * @var string
     */
    private $basePath;
    /**
     * @var array
     */
    private $errorEvents = [];

    public function __construct(IO $io, string $basePath)
    {
        $this->io = $io;
        $this->basePath = $basePath;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'afterExample' => 'logError',
            'afterSuite' => ['printErrors', -100]
        ];
    }

    public function logError(ExampleEvent $event)
    {
        if ($event->getResult() !== ExampleEvent::FAILED
            && $event->getResult() !== ExampleEvent::BROKEN) {
            return;
        }

        $this->errorEvents[] = $event;
    }

    public function printErrors(SuiteEvent $suiteEvent)
    {
        if ($this->errorEvents) {
            $this->io->write("\n");
        }

        foreach ($this->errorEvents as $event) {
            $this->io->write(
                sprintf(
                    "::error file=%s,line=%d,col=1::%s: %s\n",
                    $this->getSpecFilename($event),
                    $event->getExample()->getLineNumber(),
                    $event->getResult() === ExampleEvent::FAILED ? 'Failed' : 'Broken',
                    $this->escapeMessage($event->getMessage())
                )
            );
        }
    }

    private function getSpecFilename(ExampleEvent $event)
    {
        $specFilename = $event->getSpecification()->getResource()->getSpecFilename();

        if (strpos($specFilename, $this->basePath) === 0) {
            $specFilename = ltrim(substr($specFilename, strlen($this->basePath)), '/');
        }

        return $specFilename;
    }

    private function escapeMessage(string $message) : string
    {
        return strtr($message, ["%" => "%25", "\r" => '%0D', "\n" => '%0A']);
    }
}
