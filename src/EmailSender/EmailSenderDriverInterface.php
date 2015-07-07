<?php

namespace Smart\EmailQueue\EmailSender;

use Smart\EmailQueue\Model\Doctrine\EmailQueueEntity;

interface EmailSenderDriverInterface
{
    /**
     * @param EmailQueueEntity $emailQueue
     * @return bool
     */
    public function send(EmailQueueEntity $emailQueue);
}
