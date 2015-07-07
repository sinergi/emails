<?php

namespace Smart\EmailQueue\EmailDriver;

use Smart\EmailQueue\EmailQueueEntity;

interface EmailDriverInterface
{
    /**
     * @param EmailQueueEntity $emailQueue
     * @return bool
     */
    public function send(EmailQueueEntity $emailQueue);
}
