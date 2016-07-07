<?php

namespace Sinergi\Emails;

use Interop\Container\ContainerInterface;
use Omnimail\Email;
use Omnimail\EmailInterface;
use Omnimail\EmailSenderInterface;
use PDO;
use Sinergi\Emails\Emails\EmailsRepository;

class EmailSender
{
    public function __construct(PDO $connection, EmailSenderInterface $emailSender)
    {
        $this->connection = $connection;
        $this->emailsRepository = new EmailsRepository($connection);
        $this->emailSender = $emailSender;
    }

    public function send(EmailInterface $email, bool $storeAttachments = false)
    {
        $sent = false;
        try {
            $this->emailSender->send($email);
            $sent = true;
        } catch (\Exception $e) {
            $this->saveEmail($email, false, $e->getMessage(), $storeAttachments);
            throw $e;
        }

        if ($sent) {
            $this->saveEmail($email, true, null, $storeAttachments);
        } else {
            $this->saveEmail($email, false, 'Unknown', $storeAttachments);
        }
    }

    private function saveEmail(
        EmailInterface $email,
        bool $isSent,
        string $error = null,
        bool $storeAttachments = false
    ) {
        $this->emailsRepository->save($email, $isSent, $error, $storeAttachments);
    }
}
