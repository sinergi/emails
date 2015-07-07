<?php

namespace Smart\EmailQueue\Worker;

interface WorkerDriverInterface
{
    /**
     * @param string $jobName
     * @return bool
     */
    public function execute($jobName);
}
