<?php
namespace Sinergi\EmailQueue\Driver;

use Sinergi\EmailQueue\EmailQueueEntity;

interface DriverInterface
{
    /**
     * @param EmailQueueEntity $emailQueue
     * @return bool
     */
    public function send(EmailQueueEntity $emailQueue);
}
