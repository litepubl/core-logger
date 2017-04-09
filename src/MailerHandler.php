<?php
namespace litepubl\core\logfactory;

use Monolog\Handler\MailHandler;
use Monolog\Logger;
use litepubl\mailer\MailerInterface;

class MailerHandler extends MailHandler
{
protected $mailer;
    protected $subject;

    public function __construct(MailerInterface $mailer, string $subject, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);
$this->mailer = $mailer;
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    protected function send($content, array $records)
    {
        $content = wordwrap($content, 70);
            $this->mailer->sendToAdmin($this->subject, $content);
    }
}
