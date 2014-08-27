<?php
namespace Sinergi\EmailQueue;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Sinergi\EmailQueue\Attachement\AttachementEntity;

/**
 * @Entity(repositoryClass="Sinergi\EmailQueue\EmailQueueRepository")
 * @Table(name="email_queue")
 */
class EmailQueueEntity
{
    const LOCK_TIME = 'PT120S';

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @OneToMany(
     *   targetEntity="Sinergi\EmailQueue\Attachement\AttachementEntity",
     *   mappedBy="emailQueue",
     *   cascade={"persist"}
     * )
     * @var AttachementEntity[]
     **/
    private $attachements;

    /**
     * @Column(type="string", nullable=true, length=255)
     * @var string|null
     */
    private $from_name = null;

    /**
     * @Column(type="string", length=255)
     * @var string
     */
    private $from_email;

    /**
     * @Column(type="array", name="`to`")
     * @var array
     */
    private $to = null;

    /**
     * @Column(type="array")
     * @var array
     */
    private $cc = null;

    /**
     * @Column(type="array")
     * @var array
     */
    private $bcc = null;

    /**
     * @Column(type="string", length=255)
     * @var string
     */
    private $subject;

    /**
     * @Column(type="text")
     * @var string
     */
    private $body;

    /**
     * @Column(type="datetime")
     * @var DateTime
     */
    private $created_datetime = null;

    /**
     * @Column(type="boolean")
     * @var bool
     */
    private $is_locked = false;

    /**
     * @Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $locked_datetime = null;

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
     * @param DateTime $created_datetime
     * @return $this
     */
    public function setCreatedDatetime(DateTime $created_datetime)
    {
        $this->created_datetime = $created_datetime;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedDatetime()
    {
        return $this->created_datetime;
    }

    /**
     * @param string $from_email
     * @return $this
     */
    public function setFromEmail($from_email)
    {
        $this->from_email = $from_email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->from_email;
    }

    /**
     * @param null|string $from_name
     * @return $this
     */
    public function setFromName($from_name = null)
    {
        $this->from_name = $from_name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFromName()
    {
        return $this->from_name;
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
     * @param bool $is_locked
     * @return $this
     */
    public function setIsLocked($is_locked)
    {
        $this->is_locked = $is_locked;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsLocked()
    {
        return $this->is_locked;
    }

    /**
     * @param DateTime|null $locked_datetime
     * @return $this
     */
    public function setLockedDatetime(DateTime $locked_datetime = null)
    {
        $this->locked_datetime = $locked_datetime;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLockedDatetime()
    {
        return $this->locked_datetime;
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
