<?php

namespace Smart\EmailQueue\EmailQueueSendJob;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityRepository;
use Smart\EmailQueue\EmailSender\EmailSenderDriverInterface;
use Smart\EmailQueue\Model\Doctrine\EmailQueueRepository;
use Smart\EmailQueue\EmailQueueSender;

class EmailQueueSendJob
{
    const JOB_NAME = 'emailqueue:send';
    const TIMEOUT = 60;

    /**
     * @var EmailSenderDriverInterface
     */
    private $emailSender;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EmailQueueRepository
     */
    private $emailQueueRepository;

    /**
     * @var LoggerInterface
     */
    private $emailQueueLogger;

    /**
     * @param EmailSenderDriverInterface $emailSender
     * @param EntityManager $entityManager
     * @param EmailQueueRepository|EntityRepository $emailQueueRepository
     * @param null|LoggerInterface $emailQueueLogger
     */
    public function __construct(
        EmailSenderDriverInterface $emailSender,
        EntityManager $entityManager,
        EmailQueueRepository $emailQueueRepository,
        LoggerInterface $emailQueueLogger = null
    )
    {
        $this->emailSender = $emailSender;
        $this->entityManager = $entityManager;
        $this->emailQueueRepository = $emailQueueRepository;
        $this->emailQueueLogger = $emailQueueLogger;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::JOB_NAME;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->entityManager->getConnection()->close();
        $this->entityManager->getConnection()->connect();

        $timeStart = time();
        $this->entityManager->flush();
        $this->entityManager->clear();

        $emails = $this->emailQueueRepository->findAllUnlocked();
        foreach ($emails as $email) {
            $this->entityManager->refresh($email);
            $email->lock();
            $this->entityManager->persist($email);
        }

        $this->entityManager->flush();

        foreach ($emails as $email) {
            if (time() > ($timeStart + self::TIMEOUT)) {
                break;
            }

            $emailSender = (new EmailQueueSender($this->emailSender, $this->emailQueueLogger));

            if ($emailSender->send($email)) {
                $this->entityManager->getConnection()->close();
                $this->entityManager->getConnection()->connect();

                $this->entityManager->remove($email);
                $this->entityManager->flush($email);
            }
        }

        $this->entityManager->getConnection()->close();
    }
}
