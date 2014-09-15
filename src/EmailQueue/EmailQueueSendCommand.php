<?php
namespace Sinergi\EmailQueue;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sinergi\Gearman\Dispatcher;

class EmailQueueSendCommand extends Command
{
    const COMMAND_NAME = 'emailqueue:send';

    /**
     * @var Dispatcher
     */
    private $gearmanDispatcher;

    public function __construct(Dispatcher $gearmanDispatcher)
    {
        $this->gearmanDispatcher = $gearmanDispatcher;
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
        $output->write('Sending email queue to gearman: ');
        $this->getGearmanDispatcher()->execute(EmailQueueSendJob::JOB_NAME, null, null, EmailQueueSendJob::JOB_NAME);
        $output->write('[ <fg=green>DONE</fg=green> ]', true);
    }

    /**
     * @return Dispatcher
     */
    public function getGearmanDispatcher()
    {
        return $this->gearmanDispatcher;
    }

    /**
     * @param Dispatcher $gearmanDispatcher
     * @return $this
     */
    public function setGearmanDispatcher(Dispatcher $gearmanDispatcher)
    {
        $this->gearmanDispatcher = $gearmanDispatcher;
        return $this;
    }
}
