<?php

namespace Smart\EmailQueue;

use Exception;
use Psr\Log\LoggerInterface;
use Smart\EmailQueue\Driver\DriverInterface;

class EmailQueueSender
{
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var LoggerInterface
     */
    protected $emailQueueLogger;

    /**
     * @param DriverInterface $driver
     * @param LoggerInterface $emailQueueLogger
     */
    public function __construct(
        DriverInterface $driver,
        LoggerInterface $emailQueueLogger = null
    )
    {
        $this->driver = $driver;
        $this->emailQueueLogger = $emailQueueLogger;
    }

    /**
     * @param EmailQueueEntity $emailQueue
     * @return bool
     */
    public function send(EmailQueueEntity $emailQueue)
    {
        try {
            if ($this->driver->send($emailQueue)) {
                $this->emailQueueLogger->info($emailQueue->getBody(), [
                    'fromName' => $emailQueue->getFromName(),
                    'fromEmail' => $emailQueue->getFromEmail(),
                    'to' => $emailQueue->getTo(),
                    'cc' => $emailQueue->getCc(),
                    'bcc' => $emailQueue->getBcc(),
                    'subject' => $emailQueue->getSubject(),
                    'attachements' => $emailQueue->getAttachements()
                ]);
                return true;
            }
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->emailQueueLogger->error($this->errorMessage, [
                'fromName' => $emailQueue->getFromName(),
                'fromEmail' => $emailQueue->getFromEmail(),
                'to' => $emailQueue->getTo(),
                'cc' => $emailQueue->getCc(),
                'bcc' => $emailQueue->getBcc(),
                'subject' => $emailQueue->getSubject(),
                'attachements' => $emailQueue->getAttachements()
            ]);
            return false;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->errorMessage;
    }
}