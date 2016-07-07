<?php

namespace Sinergi\Emails\Attachments;

use Omnimail\Attachment;
use Omnimail\AttachmentInterface;
use Omnimail\EmailInterface;
use PDO;
use Sinergi\Emails\Driver\MySQL;

class AttachmentsRepository
{
    public function __construct(PDO $connection)
    {
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

        if (!$this->driver->checkTable('emails_attachments')) {
            $this->driver->createAttachmentsTable();
        }
    }

    public function save(AttachmentInterface $attachment, int $emailId)
    {
        try {
            $content = null;
            if ($attachment->getPath()) {
                $content = file_get_contents($attachment->getPath());
            } elseif ($attachment->getContent()) {
                $content = $attachment->getContent();
            }

            $this->driver->insert('emails_attachments', [
                'email_id' => $emailId,
                'name' => $attachment->getName(),
                'mime_type' => $attachment->getMimeType(),
                'content' => $content
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Could not insert email attachment in database', null, $e);
        }
    }
}
