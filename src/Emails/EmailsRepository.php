<?php

namespace Sinergi\Emails\Emails;

use Omnimail\Attachment;
use Omnimail\EmailInterface;
use PDO;
use Sinergi\Emails\Attachments\AttachmentsRepository;
use Sinergi\Emails\Driver\MySQL;
use DateTime;

class EmailsRepository
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;

        switch ($connection->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                $this->driver = new MySQL($connection);
                break;
            default:
                throw new \Exception(
                    'Driver ' . $connection->getAttribute(PDO::ATTR_DRIVER_NAME) . ' not yet implemented'
                );
        }

        if (!$this->driver->checkTable('emails')) {
            $this->driver->createEmailsTable();
        }
    }

    public function save(EmailInterface $email, bool $isSent, string $error = null, bool $storeAttachments = false)
    {
        try {
            $id = $this->driver->insert('emails', [
                'from' => $this->mapEmail($email->getFrom()),
                'subject' => $email->getSubject(),
                'text_body' => $email->getTextBody(),
                'html_body' => $email->getHtmlBody(),
                'to' => $this->mapEmails($email->getTos()),
                'cc' => $this->mapEmails($email->getCcs()),
                'bcc' => $this->mapEmails($email->getBccs()),
                'reply_to' => $this->mapEmails($email->getReplyTos()),
                'is_sent' => $isSent,
                'has_error' => !$isSent,
                'errors' => $error,
                'sent_date_time' => (new DateTime())->format('Y-m-d H:i:s'),
                'creation_date_time' => (new DateTime())->format('Y-m-d H:i:s'),
            ]);
            if ($storeAttachments && $email->getAttachements()) {
                $attachmentsRepository = new AttachmentsRepository($this->connection);
                if (count($email->getAttachements())) {
                    foreach ($email->getAttachements() as $attachement) {
                        $attachmentsRepository->save($attachement, $id);
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('Could not insert email in database', null, $e);
        }
    }

    private function mapEmails(array $emails)
    {
        $finalEmails = '';
        foreach ($emails as $email) {
            $finalEmails .= $this->mapEmail($email) . ', ';
        }
        return !empty($finalEmails) ? substr($finalEmails, 0, -2) : '';
    }

    private function mapEmail($email)
    {
        if (!empty($email['name'])) {
            return "'{$email['name']}' <{$email['email']}>";
        } else {
            return $email['email'];
        }
    }
}
