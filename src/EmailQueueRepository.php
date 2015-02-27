<?php

namespace Smart\EmailQueue;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityRepository;

class EmailQueueRepository extends EntityRepository
{
    /**
     * @return EmailQueueEntity[]
     */
    public function findAllUnlocked()
    {
        $twoMinutesAgo = (new DateTime)->sub(new DateInterval(EmailQueueEntity::LOCK_TIME));

        return $this->createQueryBuilder('e')
            ->where('e.isLocked = 0')
            ->orWhere('e.lockedDatetime < :twoMintuesAgo')
            ->setParameter('twoMintuesAgo', $twoMinutesAgo)
            ->getQuery()
            ->getResult();
    }
}
