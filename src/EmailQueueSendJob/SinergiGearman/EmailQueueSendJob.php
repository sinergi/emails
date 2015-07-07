<?php

namespace Smart\EmailQueue\EmailQueueSendJob\SinergiGearman;

use GearmanJob;
use Sinergi\Gearman\JobInterface;

class EmailQueueSendJob extends \Smart\EmailQueue\EmailQueueSendJob\EmailQueueSendJob implements JobInterface
{
    /**
     * @param GearmanJob|null $job
     * @return void
     */
    public function execute(GearmanJob $job = null)
    {
        parent::execute();
    }
}
