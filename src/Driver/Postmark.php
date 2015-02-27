<?php

namespace Smart\EmailQueue\Driver;

use Exception;
use Postmark\Mail;
use Smart\EmailQueue\EmailQueueEntity;

class Postmark implements DriverInterface
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->setApiKey($apiKey);
    }

    /**
     * @param EmailQueueEntity $emailQueue
     * @return bool
     * @throws Exception
     */
    public function send(EmailQueueEntity $emailQueue)
    {
        $email = new Mail($this->getApiKey());
        $email->from($emailQueue->getFromEmail(), $emailQueue->getFromName());

        foreach ($emailQueue->getTo() as $to) {
            $email->addTo($to['email'], $to['name']);
        }

        foreach ($emailQueue->getCc() as $cc) {
            $email->addCc($cc['email'], $cc['name']);
        }

        foreach ($emailQueue->getBcc() as $bcc) {
            $email->addBcc($bcc['email'], $bcc['name']);
        }

        $email->subject($emailQueue->getSubject());
        $email->messageHtml($emailQueue->getBody());

        foreach ($emailQueue->getAttachements() as $attachement) {
            $email->addCustomAttachment(
                $attachement->getName(),
                stream_get_contents($attachement->getFile()),
                $attachement->getMimeType()
            );
        }

        return $email->send();
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }
}
