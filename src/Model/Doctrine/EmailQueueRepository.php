<?php

namespace Smart\EmailQueue\Model\Doctrine;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Smart\EmailQueue\Model\EmailQueueEntityInterface;
use Smart\EmailQueue\Model\EmailQueueRepositoryInterface;

class EmailQueueRepository extends EntityRepository implements EmailQueueRepositoryInterface
{
    /**
     * @return EmailQueueEntityInterface[]
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
