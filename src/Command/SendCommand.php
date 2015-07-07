<?php

namespace Smart\EmailQueue\Command;

use Smart\EmailQueue\DispatcherInterface;
use Smart\EmailQueue\EmailQueueSendJob\EmailQueueSendJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends Command
{
    const COMMAND_NAME = 'emailqueue:send';

    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription('This sends the email queue');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Sending email queue: ');
        $this->getDispatcher()->dispatch(EmailQueueSendJob::JOB_NAME);
        $output->write('[ <fg=green>DONE</fg=green> ]', true);
    }

    /**
     * @return DispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param DispatcherInterface $dispatcher
     * @return $this
     */
    public function setGearmanDispatcher(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }
}
