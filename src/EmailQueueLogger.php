<?php

namespace Smart\EmailQueue;

use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Sinergi\Token\StringGenerator;
use Smart\EmailQueue\Attachement\AttachementEntity;

class EmailQueueLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private $logDir;

    /**
     * @var string
     */
    private $logFile;

    /**
     * @param string $logFile
     * @param string $logDir
     */
    public function __construct($logFile, $logDir)
    {
        $this->logFile = $logFile;
        $this->logDir = $logDir;
    }

    /**
     * @param string $message
     * @param array $context
     */
    private function logEmail($message, array $context = [])
    {
        if ($this->logDir === null) {
            return;
        }

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir);
        }

        $slugify = new Slugify;

        $email = '';
        if (isset($context['to'][0]['email'])) {
            $email = $context['to'][0]['email'];
        }

        $file = date('Y-m-d H-i-s ') .
            substr($slugify->slugify($email), 0, 28) . ' ' .
            substr($slugify->slugify($context['subject']), 0, 28);
        $file = $this->logDir . DIRECTORY_SEPARATOR . $file;

        $originFile = $file;
        $count = 1;
        while (file_exists($file . '.txt')) {
            $file = $originFile . $count;
            $count++;
            if ($count >= 100) {
                break;
            }
        }

        $text = explode('<body', $message, 2);
        $text = isset($text[1]) ? $text[1] : '';
        if (!empty($text)) {
            $text = explode('>', $text, 2);
            $text = isset($text[1]) ? $text[1] : '';
            $text = trim(strip_tags($text));
            $lines = explode(PHP_EOL, $text);
            $text = '';
            $firstEmptyLine = true;
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $firstEmptyLine = true;
                    $text .= $line . PHP_EOL;
                } elseif ($firstEmptyLine) {
                    $firstEmptyLine = false;
                    $text .= PHP_EOL;
                }
            }
        }

        $body = "";
        $body .= "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
        $body .= "From: {$context['fromName']} <{$context['fromEmail']}>" . PHP_EOL;

        if (isset($context['to']) && count($context['to'])) {
            $body .= "To: ";
            foreach ($context['to'] as $to) {
                $body .= "{$to['name']} <{$to['email']}>, ";
            }

            $body = substr($body, 0, -2) . PHP_EOL;;
        }

        if (isset($context['cc']) && count($context['cc'])) {
            $body .= "CC: ";
            foreach ($context['cc'] as $cc) {
                $body .= "{$cc['name']} <{$cc['email']}>, ";
            }

            $body = substr($body, 0, -2) . PHP_EOL;;
        }

        if (isset($context['bcc']) && count($context['bcc'])) {
            $body .= "BCC: ";
            foreach ($context['bcc'] as $bcc) {
                $body .= "{$bcc['name']} <{$bcc['email']}>, ";
            }

            $body = substr($body, 0, -2) . PHP_EOL;;
        }

        $body .= "Subject: {$context['subject']}" . PHP_EOL;

        if (isset($context['attachements']) && count($context['attachements'])) {
            $body .= "Attachements: ";
            $attachementDir = $this->logDir . DIRECTORY_SEPARATOR . 'attachements';
            if (!is_dir($attachementDir)) {
                mkdir($attachementDir);
            }

            /** @var AttachementEntity $attachement */
            foreach ($context['attachements'] as $attachement) {
                $filename = pathinfo($attachement->getName(), PATHINFO_EXTENSION);
                $filename = substr(
                    $slugify->slugify(substr($attachement->getName(), 0, -(strlen($filename)))),
                    0, (128 - strlen($filename))
                ) . (!empty($filename) ? '.' . $filename : '');
                $filename = StringGenerator::randomAlnum(8) . '_' . $filename;
                $body .= "{$attachement->getName()} ({$attachement->getMimeType()}, {$filename}), ";
                file_put_contents(
                    $attachementDir . DIRECTORY_SEPARATOR . $filename,
                    stream_get_contents($attachement->getFile())
                );
            }
            $body = substr($body, 0, -2) . PHP_EOL;;
        }

        $body .= PHP_EOL;
        $body .= "Text Version: " . PHP_EOL;
        $body .= "-------------------------------" . PHP_EOL;
        $body .= PHP_EOL;
        $body .= trim($text) . PHP_EOL;
        $body .= PHP_EOL;
        $body .= "Html Version: " . PHP_EOL;
        $body .= "-------------------------------" . PHP_EOL;
        $body .= PHP_EOL;
        $body .= trim($message);

        file_put_contents($file . '.txt', $body);
    }

    /**
     * @param string $level
     * @param string $message
     */
    private function writeLog($level, $message)
    {
        if (!empty($message)) {
            if ($this->logFile === null) {
                return;
            }
            $content = date('Y-m-d H:i:s') . ' ' . $level . ': ' . $message . PHP_EOL;
            file_put_contents($this->logFile, $content, FILE_APPEND);
        }
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = [])
    {
        $this->writeLog('Emergency', $message);
    }

    /**
     * Action must be taken immediately.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = [])
    {
        $this->writeLog('Alert', $message);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = [])
    {
        $this->writeLog('Critical', $message);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = [])
    {
        if (isset($context['to'][0]['email'])) {
            $this->writeLog('Error', "Error sending email to \"{$context['to'][0]['email']}\": {$message}");
        } else {
            $this->writeLog('Error', "Error sending email: {$message}");
        }
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = [])
    {
        $this->writeLog('Warning', $message);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = [])
    {
        $this->writeLog('Notice', $message);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = [])
    {
        if (isset($context['to'][0]['email'])) {
            $this->writeLog('Info', "Sent email to \"{$context['to'][0]['email']}\" with subject \"{$context['subject']}\"");
            $this->logEmail($message, $context);
        } else {
            $this->error("No recipient address provided", $context);
        }
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = [])
    {
        $this->writeLog('Debug', $message);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog('Log', $message);
    }
}
