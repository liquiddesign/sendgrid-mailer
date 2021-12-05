<?php

declare(strict_types=1);

namespace SendgridMailer;

use Nette\Mail\Message;

class Mailer implements \Nette\Mail\Mailer
{
	private \SendGrid $sendgrid;
	
	private MessageFactory $messageFactory;
	
	public function __construct(string $apiKey, MessageFactory $messageFactory)
	{
		$this->sendgrid = new \SendGrid($apiKey);
		$this->messageFactory = $messageFactory;
	}
	
	public function send(Message $mail): void
	{
		$response = $this->sendgrid->send($this->messageFactory->createMail($mail));
		$response->statusCode();
	}
}
