<?php

namespace Smart\EmailQueue\WorkerDriver;

interface WorkerDriverInterface
{
    /**
     * @param string $jobName
     * @return bool
     */
    public function execute($jobName);
}
