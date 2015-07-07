<?php

namespace Smart\EmailQueue;

use Exception;
use Psr\Log\LoggerInterface;
use Smart\EmailQueue\EmailSender\EmailSenderDriverInterface;
use Smart\EmailQueue\Model\Doctrine\EmailQueueEntity;
use Smart\EmailQueue\Model\EmailQueueEntityInterface;

class EmailQueueSender
{
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var EmailSenderDriverInterface
     */
    protected $emailSender;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param EmailSenderDriverInterface $emailSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        EmailSenderDriverInterface $emailSender,
        LoggerInterface $logger = null
    )
    {
        $this->emailSender = $emailSender;
        $this->logger = $logger;
    }

    /**
     * @param EmailQueueEntity|EmailQueueEntityInterface $emailQueue
     * @return bool
     */
    public function send(EmailQueueEntityInterface $emailQueue)
    {
        try {
            if ($this->emailSender->send($emailQueue)) {
                $this->logger->info($emailQueue->getBody(), [
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
            $this->logger->error($this->errorMessage, [
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
