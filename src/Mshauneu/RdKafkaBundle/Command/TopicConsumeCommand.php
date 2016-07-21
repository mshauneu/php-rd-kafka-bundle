<?php

namespace Mshauneu\RdKafkaBundle\Command;

use Mshauneu\RdKafkaBundle\Topic\TopicCommunicator;
use Mshauneu\RdKafkaBundle\Topic\TopicConsumer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Consume Command
 * 
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
class TopicConsumeCommand extends ContainerAwareCommand {
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {
		$this
			->setName('kafka:consumer')
			->addOption('consumer', null, InputOption::VALUE_REQUIRED, 'Consumer')
			->addOption('handler', null, InputOption::VALUE_REQUIRED, 'MessageHandler')
			->addOption('partition', 'p', InputOption::VALUE_OPTIONAL, 'Partition')
			->addOption('offset', 'o', InputOption::VALUE_OPTIONAL, 'Offset')
			->addOption('timeout', 't', InputOption::VALUE_OPTIONAL, 'Timeout in ms')
		;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
    protected function execute(InputInterface $input, OutputInterface $output) {
		$consumer = $input->getOption('consumer');
		
		$topicConsumer = $this->getContainer()->get('mshauneu_rd_kafka')->getConsumer($consumer);
		if (!$topicConsumer) {
			throw new \Exception(sprintf("TopicConsumer with name '%s' is not defined", $consumer));
		}

		$handler = $input->getOption('handler');
		$messageHandler = $this->getContainer()->get($handler);
		if (!$messageHandler) {
			throw new \Exception(sprintf("Message Handler with name '%s' is not defined", $handler));
		}
		
		$partition = $input->getOption('partition');
		if ($partition) {
			if(!is_numeric($partition) || $partition < 0) {
				throw new \Exception("Partition needs to be a number in the range 0..2^32-1");
			}
		} else {
			$partition = 0;
		}

		$offset = $input->getOption('offset');
		if ($offset) {
			if(!is_numeric($offset)) {
				throw new \Exception("Offset needs to be a number");
			}
		} else {
			$offset = TopicCommunicator::OFFSET_BEGINNING;
		}

		$timeout = $input->getOption('timeout');
		if ($timeout) {
			if (!is_numeric($timeout)) {
				throw new \Exception("Timeout needs to be a number in the range 0..2^32-1");
			}
		} else {
			$timeout = 1000;
		}
		
		$topicConsumer->consumeStart($offset, $partition);
		$topicConsumer->consume($messageHandler, $partition, $timeout);
		$topicConsumer->consumeStop();
	}

}

