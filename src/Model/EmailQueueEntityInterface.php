<?php

namespace Smart\EmailQueue\Model;

use DateTime;

interface EmailQueueEntityInterface
{
    public function lock();

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param DateTime $createdDatetime
     * @return $this
     */
    public function setCreatedDatetime(DateTime $createdDatetime);

    /**
     * @return DateTime
     */
    public function getCreatedDatetime();

    /**
     * @param string $fromEmail
     * @return $this
     */
    public function setFromEmail($fromEmail);

    /**
     * @return string
     */
    public function getFromEmail();

    /**
     * @param null|string $fromName
     * @return $this
     */
    public function setFromName($fromName = null);

    /**
     * @return null|string
     */
    public function getFromName();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param bool $isLocked
     * @return $this
     */
    public function setIsLocked($isLocked);

    /**
     * @return bool
     */
    public function isLocked();

    /**
     * @param DateTime|null $lockedDatetime
     * @return $this
     */
    public function setLockedDatetime(DateTime $lockedDatetime = null);

    /**
     * @return DateTime|null
     */
    public function getLockedDatetime();

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @return AttachementEntityInterface[]
     */
    public function getAttachements();

    /**
     * @param AttachementEntityInterface[] $attachements
     * @return $this
     */
    public function setAttachements($attachements);

    /**
     * @return array
     */
    public function getBcc();

    /**
     * @param array $bcc
     * @return $this
     */
    public function setBcc($bcc);

    /**
     * @return array
     */
    public function getCc();

    /**
     * @param array $cc
     * @return $this
     */
    public function setCc($cc);

    /**
     * @return array
     */
    public function getTo();

    /**
     * @param array $to
     * @return $this
     */
    public function setTo($to);
}
