<?php

namespace Sinergi\Emails;

use Omnimail\EmailInterface;

interface EmailSenderInterface
{
    public function send(EmailInterface $email, bool $storeAttachments = false);
}
