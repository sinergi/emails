<?php

namespace Sinergi\Emails\Driver;

use PDO;
use Exception;

class MySQL
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function insert($table, $data): int
    {
        $query = "INSERT INTO `{$table}` (";
        foreach ($data as $key => $value) {
            $query .= "`{$key}`, ";
        }
        $query = substr($query, 0, -2) . ') VALUES (';
        foreach ($data as $key => $value) {
            $query .= ":{$key}, ";
        }
        $query = substr($query, 0, -2) . ')';

        $sth = $this->connection->prepare($query);

        $finalParams = [];
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }
            $finalParams[':' . $key] = $value;
        }

        $result = $sth->execute($finalParams);
        if (!$result) {
            throw new Exception('Could not insert row in ' . $table);
        }
        return $this->connection->lastInsertId();
    }

    public function checkTable($name): bool
    {
        try {
            $result = $this->connection->query("SELECT 1 FROM $name LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
        return $result !== false;
    }

    public function createEmailsTable()
    {
        $this->createTable('emails', <<<SQL
CREATE TABLE `emails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from` text,
  `subject` text,
  `text_body` text,
  `html_body` text,
  `to` text,
  `cc` text,
  `bcc` text,
  `reply_to` text,
  `is_sent` tinyint(1) DEFAULT NULL,
  `has_error` tinyint(1) DEFAULT NULL,
  `errors` text,
  `sent_date_time` datetime DEFAULT NULL,
  `creation_date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
)
SQL
        );
    }

    public function createAttachmentsTable()
    {
        $this->createTable('emails_attachments', <<<SQL
CREATE TABLE `emails_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(64) DEFAULT NULL,
  `content` longblob,
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  CONSTRAINT `email_relation` FOREIGN KEY (`email_id`) REFERENCES `emails` (`id`)
)
SQL
        );
    }

    private function createTable($name, $query)
    {
        try {
            $result = $this->connection->query($query);
        } catch (Exception $e) {
            throw new Exception('Could not create ' . $name . ' table');
        }
        if ($result === false) {
            throw new Exception('Could not create ' . $name . ' table');
        }
    }
}
