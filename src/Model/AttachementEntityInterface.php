<?php

namespace Smart\EmailQueue\Model;

interface AttachementEntityInterface
{
    /**
     * @return EmailQueueEntityInterface
     */
    public function getEmailQueue();

    /**
     * @param EmailQueueEntityInterface $emailQueue
     * @return $this
     */
    public function setEmailQueue(EmailQueueEntityInterface $emailQueue);

    /**
     * @return resource
     */
    public function getFile();

    /**
     * @param resource $file
     * @return $this
     */
    public function setFile($file);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param string $mimeType
     * @return $this
     */
    public function setMimeType($mimeType);
}
