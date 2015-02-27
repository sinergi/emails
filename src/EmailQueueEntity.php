<?php

namespace Smart\EmailQueue;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Smart\EmailQueue\Attachement\AttachementEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Smart\EmailQueue\EmailQueueRepository")
 * @ORM\Table(name="email_queue")
 */
class EmailQueueEntity
{
    const LOCK_TIME = 'PT120S';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\OneToMany(
     *   targetEntity="Smart\EmailQueue\Attachement\AttachementEntity",
     *   mappedBy="emailQueue",
     *   cascade={"persist"}
     * )
     * @var AttachementEntity[]
     **/
    private $attachements;

    /**
     * @ORM\Column(type="string", name="from_name", nullable=true, length=255)
     * @var string|null
     */
    private $fromName = null;

    /**
     * @ORM\Column(type="string", name="from_email", length=255)
     * @var string
     */
    private $fromEmail;

    /**
     * @ORM\Column(type="array", name="`to`")
     * @var array
     */
    private $to = null;

    /**
     * @ORM\Column(type="array")
     * @var array
     */
    private $cc = null;

    /**
     * @ORM\Column(type="array")
     * @var array
     */
    private $bcc = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $subject;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $body;

    /**
     * @ORM\Column(type="datetime", name="created_datetime")
     * @var DateTime
     */
    private $createdDatetime = null;

    /**
     * @ORM\Column(type="boolean", name="is_locked")
     * @var bool
     */
    private $isLocked = false;

    /**
     * @ORM\Column(type="datetime", name="locked_datetime", nullable=true)
     * @var DateTime|null
     */
    private $lockedDatetime = null;

    public function __construct()
    {
        $this->setCreatedDatetime(new DateTime('now'));
        $this->setAttachements(new ArrayCollection());
    }

    public function lock()
    {
        $this->setIsLocked(true);
        $this->setLockedDatetime(new DateTime('now'));
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
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param DateTime $createdDatetime
     * @return $this
     */
    public function setCreatedDatetime(DateTime $createdDatetime)
    {
        $this->createdDatetime = $createdDatetime;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedDatetime()
    {
        return $this->createdDatetime;
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
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param null|string $fromName
     * @return $this
     */
    public function setFromName($fromName = null)
    {
        $this->fromName = $fromName;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param bool $isLocked
     * @return $this
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * @param DateTime|null $lockedDatetime
     * @return $this
     */
    public function setLockedDatetime(DateTime $lockedDatetime = null)
    {
        $this->lockedDatetime = $lockedDatetime;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLockedDatetime()
    {
        return $this->lockedDatetime;
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
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return AttachementEntity[]|ArrayCollection
     */
    public function getAttachements()
    {
        return $this->attachements;
    }

    /**
     * @param AttachementEntity[]|ArrayCollection $attachements
     * @return $this
     */
    public function setAttachements($attachements)
    {
        $this->attachements = $attachements;
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
     * @param array $bcc
     * @return $this
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
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
     * @param array $cc
     * @return $this
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
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
     * @param array $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }
}
