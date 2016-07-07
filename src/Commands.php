<?php

namespace Sinergi\Emails;

use Interop\Container\ContainerInterface;

class Commands
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke()
    {
        return [
            
        ];
    }
}
