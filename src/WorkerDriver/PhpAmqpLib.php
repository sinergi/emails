<?php

namespace Smart\EmailQueue\WorkerDriver;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class PhpAmqpLib implements WorkerDriverInterface
{
    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @param AMQPChannel $channel
     */
    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param string $jobName
     * @return void
     */
    public function execute($jobName)
    {
        $this->channel->queue_declare($jobName, false, false, false, false);
        $this->channel->basic_publish(new AMQPMessage, '', $jobName);
    }
}
