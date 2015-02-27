<?php

namespace Smart\EmailQueue\Driver;

use Smart\EmailQueue\EmailQueueEntity;

interface DriverInterface
{
    /**
     * @param EmailQueueEntity $emailQueue
     * @return bool
     */
    public function send(EmailQueueEntity $emailQueue);
}
