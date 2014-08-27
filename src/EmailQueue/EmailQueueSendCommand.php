<?php
namespace Sinergi\EmailQueue;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmailQueueSendCommand extends Command
{
    const COMMAND_NAME = 'emailqueue:send';

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
}
