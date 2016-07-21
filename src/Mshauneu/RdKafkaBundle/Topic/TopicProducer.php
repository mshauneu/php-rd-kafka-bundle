<?php

namespace Mshauneu\RdKafkaBundle\Topic;

use RdKafka\Producer;
use RdKafka\ProducerTopic;

/**
 * TopicProducer
 *
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
class TopicProducer extends TopicCommunicator {

	
	/**
	 * @var bool
	 */
	protected $isProducing = false;

	/**
	 * 
	 * @var ProducerTopic
	 */
	protected $producerTopic;
	
	/**
	 * @throws \Exception
	 */
	public function produceStart() {
		if ($this->isProducing === true) {
			throw new \Exception("This topic is already producing");
		}
		
		$config = $this->getConfig($this->props);
		$producer = new Producer($config);
		$producer->addBrokers($this->brokers);
		$producerTopicConf = $this->getTopicConfig($this->topicProps);
		$this->producerTopic = $producer->newTopic($this->topic, $producerTopicConf);
		
		$this->isProducing = true;
	}
	
	/**
	 * Produce and send a single message to broker
	 * 
	 * @param string $payload 
	 * 		is the message payload
	 * @param int $partition 
	 * 		is the target partition, either Topic::PARTITION_UA (unassigned) for automatic partitioning 
	 * 		using the topic's partitioner function, or a fixed partition (0..N)
	 * @param string|null $key 
	 * 		is an optional message key, if non-NULL it will be passed to the topic partitioner 
	 * 		as well as be sent with the message to the broker and passed on to the consumer.
	 * @throws \Exception
	 */
	public function produce($payload, $partition = TopicCommunicator::PARTITION_UA, $key = null) {
		if (true !== $this->isProducing) {
			throw new \Exception ("Please call produceStart first to start producing message");
		}
		
		$this->producerTopic->produce($partition, 0, $payload, $key);
	}	

	public function produceStop() {
		$this->producerTopic = null;
		$this->isProducing = false;
		
	}
	
}