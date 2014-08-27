<?php
namespace Sinergi\EmailQueue;

use Doctrine\ORM\EntityManager;
use GearmanJob;
use Sinergi\EmailQueue\Driver\DriverInterface;
use Sinergi\Gearman\JobInterface;

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
     * @var EmailQueueLogger
     */
    private $emailQueueLogger;

    /**
     * @param DriverInterface $driver
     * @param EntityManager $entityManager
     * @param EmailQueueRepository $emailQueueRepository
     * @param null|EmailQueueLogger $emailQueueLogger
     */
    public function __construct(
        DriverInterface $driver,
        EntityManager $entityManager,
        EmailQueueRepository $emailQueueRepository,
        EmailQueueLogger $emailQueueLogger = null
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
                $this->entityManager->remove($email);
                $this->entityManager->flush($email);
            }
        }
    }
}
