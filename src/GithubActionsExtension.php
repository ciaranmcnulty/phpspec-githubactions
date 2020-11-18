<?php

namespace Cjm\PhpSpec;

use Cjm\PhpSpec\GithubActions\FailedExamplesFormatter;
use PhpSpec\Extension;
use PhpSpec\ServiceContainer;
use PhpSpec\ServiceContainer\IndexedServiceContainer;

final class GithubActionsExtension implements Extension
{
    public function load(ServiceContainer $container, array $params)
    {
        if (getenv('GITHUB_ACTIONS')) {
            $container->define('event_dispatcher.listeners.github', function(IndexedServiceContainer $c) {
                return new FailedExamplesFormatter(
                    $c->get('console.io'),
                    getcwd()
                );
            }, ['event_dispatcher.listeners']);
        }
    }
}
