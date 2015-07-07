<?php

namespace Smart\EmailQueue;

interface DispatcherInterface
{
    public function dispatch($jobName);
}
