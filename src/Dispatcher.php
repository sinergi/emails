<?php

namespace Smart\EmailQueue;

use Smart\EmailQueue\Worker\WorkerDriverInterface;

class Dispatcher implements DispatcherInterface
{
    /**
     * @var WorkerDriverInterface
     */
    protected $worker;

    /**
     * @param WorkerDriverInterface $worker
     */
    public function __construct(WorkerDriverInterface $worker)
    {
        $this->worker = $worker;
    }

    /**
     * @param $jobName
     */
    public function dispatch($jobName)
    {
        $this->worker->execute($jobName);
    }
}
