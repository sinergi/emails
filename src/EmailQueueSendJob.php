<?php

namespace Smart\EmailQueue;

use Doctrine\ORM\EntityManager;
use GearmanJob;
use Psr\Log\LoggerInterface;
use Smart\EmailQueue\Driver\DriverInterface;
use Sinergi\Gearman\JobInterface;
use Doctrine\ORM\EntityRepository;

class EmailQueueSendJob implements JobInterface
{
    const JOB_NAME = 'emailqueue:send';
    const TIMEOUT = 60;

    /**
     * @var DriverInterface
     */
    private $driver;

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
     * @param DriverInterface $driver
     * @param EntityManager $entityManager
     * @param EmailQueueRepository|EntityRepository $emailQueueRepository
     * @param null|LoggerInterface $emailQueueLogger
     */
    public function __construct(
        DriverInterface $driver,
        EntityManager $entityManager,
        EmailQueueRepository $emailQueueRepository,
        LoggerInterface $emailQueueLogger = null
    )
    {
        $this->driver = $driver;
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
     * @param GearmanJob|null $job
     * @return mixed
     */
    public function execute(GearmanJob $job = null)
    {
        if (!$this->entityManager->getConnection()->isConnected()) {
            $this->entityManager->getConnection()->connect();
        }

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

            $emailSender = (new EmailQueueSender($this->driver, $this->emailQueueLogger));

            if ($emailSender->send($email)) {
                if (!$this->entityManager->getConnection()->isConnected()) {
                    $this->entityManager->getConnection()->connect();
                }

                $this->entityManager->remove($email);
                $this->entityManager->flush($email);
            }
        }

        $this->entityManager->getConnection()->close();
    }
}
