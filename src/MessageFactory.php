<?php

declare(strict_types=1);

namespace SendgridMailer;

use Nette\InvalidStateException;
use Nette\Mail\Message;
use Nette\Utils\Arrays;
use SendGrid\Mail\Mail;
use SendGrid\Mail\ReplyTo;

class MessageFactory
{
	public function createMail(Message $message): Mail
	{
		if ($message->getFrom() === null) {
			throw new InvalidStateException('getFrom() cannot return null');
		}
		
		$from = (string) \key($message->getFrom());
		
		$email = new \SendGrid\Mail\Mail();
		$email->setFrom($from, $message->getFrom()[$from] ?? null);
		
		if ($message->getSubject()) {
			$email->setSubject($message->getSubject());
		}
	
		$email->addContent('text/plain', $message->getBody());
		$email->addContent('text/html', $message->getHtmlBody());
		
		foreach ($message->getHeader('To') as $recipient => $name) {
			$email->addTo($recipient, $name);
		}
		
		if ($message->getHeader('Cc')) {
			foreach ($message->getHeader('Cc') as $recipient => $name) {
				$email->addCc($recipient, $name);
			}
		}
		
		if ($message->getHeader('Bcc')) {
			foreach ($message->getHeader('Bcc') as $recipient => $name) {
				$email->addBcc($recipient, $name);
			}
		}

		if ($message->addReplyTo('Reply-To')) {
			$firstReplyTo = \array_keys($message->getHeader('Reply-To'))[0] ?? null;
			$firstReplyToName = Arrays::first($message->getHeader('Reply-To'));

			if ($firstReplyTo) {
				$email->setReplyTo($firstReplyTo, $firstReplyToName);
			}
		}
		
		foreach ($message->getAttachments() as $attachment) {
			$output = [];
			\preg_match_all('/(.+);.*filename=\"(.+)\"/', $attachment->getHeader('Content-Disposition'), $output);
			
			$email->addAttachment(\base64_encode($attachment->getBody()), $attachment->getHeader('Content-Type'), $output[2][0] ?? 'file', $output[1][0] ?? 'attachment');
		}

		return $email;
	}
}
