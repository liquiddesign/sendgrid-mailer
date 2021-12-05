<?php

declare(strict_types=1);

namespace SendgridMailer;

use Nette\Mail\Message;
use Nette\Mail\SendException;

class Mailer implements \Nette\Mail\Mailer
{
	private const HTTP_ACCEPTED = 202;
	
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
		
		if ($response->statusCode() !== self::HTTP_ACCEPTED) {
			throw new SendException($response->body(), $response->statusCode());
		}
	}
}
