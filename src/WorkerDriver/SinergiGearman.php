<?php

namespace Smart\EmailQueue\WorkerDriver;

use Sinergi\Gearman\Dispatcher;

class SinergiGearman implements WorkerDriverInterface
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $jobName
     * @return void
     */
    public function execute($jobName)
    {
        $this->dispatcher->background($jobName, null, null, $jobName);
    }
}
