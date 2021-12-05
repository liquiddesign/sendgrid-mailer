<?php

declare(strict_types=1);

namespace SendgridMailer\Bridges;

use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SendgridMailer\Mailer;
use SendgridMailer\MessageFactory;

class SengridMailerDI extends \Nette\DI\CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'apiKey' => Expect::string()->required(),
		]);
	}
	
	public function loadConfiguration(): void
	{
		/** @var \stdClass $config */
		$config = $this->getConfig();
		
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('messageFactory'), new ServiceDefinition())
			->setType(MessageFactory::class);
		
		$builder->addDefinition($this->prefix('mailer'), new ServiceDefinition())
			->setType(Mailer::class)
			->setArguments([
				'apiKey' => $config->apiKey,
			]);
	}
}
