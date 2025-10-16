<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class RegisterService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function send(string $to, string $subject, string $message): void
    {
        // Just log for this example
        $this->logger->info("Sending email to $to: $subject - $message");

        // In real use, integrate with MailerInterface
    }
}
