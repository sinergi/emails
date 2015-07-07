<?php

namespace Smart\EmailQueue\Model;

interface EmailQueueRepositoryInterface
{
    /**
     * @return EmailQueueEntityInterface[]
     */
    public function findAllUnlocked();
}
