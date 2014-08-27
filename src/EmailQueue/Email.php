<?php
namespace Sinergi\EmailQ;

use Doctrine\ORM\EntityManager;
use Sinergi\EmailQueue\Attachement\AttachementEntity;
use Sinergi\EmailQueue\EmailQueueSendJob;
use Sinergi\EmailQueue\EmailQueueEntity;
use Sinergi\Gearman\Dispatcher;

abstract class Email
{
    /**
     * @var array
     */
    protected $to = [];

    /**
     * @var array
     */
    protected $cc = [];

    /**
     * @var array
     */
    protected $bcc = [];

    /**
     * @var array
     */
    protected $attachements = [];

    /**
     * @var string
     */
    protected $fromName;

    /**
     * @var string
     */
    protected $fromEmail;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param EntityManager $entityManager
     * @param Dispatcher $dispatcher
     */
    public function __construct(EntityManager $entityManager, Dispatcher $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return bool
     */
    public function create()
    {
        $email = new EmailQueueEntity();
        $email->setFromName($this->getFromName());
        $email->setFromEmail($this->getFromEmail());

        $email->setTo($this->getTo());
        $email->setCc($this->getCc());
        $email->setBcc($this->getBcc());

        $attachements = $email->getAttachements();
        foreach ($this->getAttachements() as $attachement) {
            $attachementEntity = new AttachementEntity();
            $attachementEntity->setEmailQueue($email);
            $attachementEntity->setName($attachement['name']);
            $attachementEntity->setFile($attachement['content']);
            $attachementEntity->setMimeType($attachement['mimeType']);
            $attachements->add($attachementEntity);
        }

        $email->setSubject($this->getSubject());
        $email->setBody($this->getBody());

        $this->entityManager->persist($email);
        $this->entityManager->flush($email);

        $this->dispatcher->background(EmailQueueSendJob::JOB_NAME, null, null, EmailQueueSendJob::JOB_NAME);
        return true;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     * @return $this
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function addTo($email, $name)
    {
        $this->to[] = [
            'email' => $email,
            'name' => $name
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function addCc($email, $name)
    {
        $this->cc[] = [
            'email' => $email,
            'name' => $name
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function addBcc($email, $name)
    {
        $this->bcc[] = [
            'email' => $email,
            'name' => $name
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getAttachements()
    {
        return $this->attachements;
    }

    /**
     * @param string $name
     * @param string $content
     * @param string $mimeType
     * @return $this
     */
    public function addAttachement($name, $content, $mimeType)
    {
        $this->attachements[] = [
            'name' => $name,
            'content' => $content,
            'mimeType' => $mimeType
        ];
        return $this;
    }

    /**
     * @return string
     */
    public function getToEmail()
    {
        if (isset($this->to[0]['email'])) {
            return $this->to[0]['email'];
        }
        return null;
    }

    /**
     * @param string $toEmail
     * @return $this
     */
    public function setToEmail($toEmail)
    {
        $this->to = array_slice($this->to, 0, 1);
        if (!isset($this->to[0])) {
            $this->to[0] = [];
        }
        $this->to[0]['email'] = $toEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getToName()
    {
        if (isset($this->to[0]['name'])) {
            return $this->to[0]['name'];
        }
        return null;
    }

    /**
     * @param string $toName
     * @return $this
     */
    public function setToName($toName)
    {
        $this->to = array_slice($this->to, 0, 1);
        if (!isset($this->to[0])) {
            $this->to[0] = [];
        }
        $this->to[0]['name'] = $toName;
        return $this;
    }
}
