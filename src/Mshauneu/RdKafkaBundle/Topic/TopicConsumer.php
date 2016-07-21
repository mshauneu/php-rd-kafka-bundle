<?php

namespace Mshauneu\RdKafkaBundle\Topic;

use RdKafka\Consumer;
use RdKafka\ConsumerTopic;

/**
 * TopicConsumer
 *
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
class TopicConsumer extends TopicCommunicator {

	/**
	 * @var bool
	 */
	protected $isConsuming = false;
	
	/**
	 * @var ConsumerTopic
	 */
	protected $consumerTopic;
	
	
	/**
	 * @param number $partition
	 * @param string $offset, @see Offset constants
	 * @throws \Exception
	 */
	public function consumeStart($offset = TopicCommunicator::OFFSET_BEGINNING, $partition = 0) {
		if ($this->isConsuming === true) {
			throw new \Exception("This topic is already consuming");
		}

		$config = $this->getConfig($this->props);
		$consumer = new Consumer($config);
		$consumer->addBrokers($this->brokers);
		$consumerTopicConf = $this->getTopicConfig($this->topicProps);
		$this->consumerTopic = $consumer->newTopic($this->topic, $consumerTopicConf);
		$this->consumerTopic->consumeStart($partition, $offset);

		$this->isConsuming = true;
	}
	
	/**
	 * @param ConsumerInterface $consumer
	 * @param number $partition
	 * @param number $timeoutInMs
	 * @throws \Exception
	 */
	public function consume($consumer, $partition = 0, $timeoutInMs = 1000) {
		if ($consumer === null) {
			throw new \Exception ("Mo Consumer impl defined");
		}
		if (true !== $this->isConsuming) {
			throw new \Exception ("Please call consumeStart first to start consuming message");
		}
		
		while ($message = $this->consumerTopic->consume($partition, $timeoutInMs)) {
			$consumer->consume($message->topic_name, $message->partition, $message->offset, $message->key, $message->payload);
		}
	}
	
	/**
	 * @param number $partition
	 */
	public function consumeStop($partition = 0) {
		$this->consumerTopic->consumeStop($partition);
		$this->consumerTopic = null;
		$this->isConsuming = false;
	}	
	
}