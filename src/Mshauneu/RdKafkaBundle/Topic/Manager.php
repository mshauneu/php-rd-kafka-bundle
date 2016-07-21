<?php

namespace Mshauneu\RdKafkaBundle\Topic;


/**
 * Manager
 *
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
class Manager {	
	
	/**
	 * @var Producer[]
	 */
	protected $producers = array();
	
	/**
	 * @var Consumer[]
	 */
	protected $consumers = array();
	

	/**
	 * @param string $name	
	 * @param $props
	 */
	public function addProducer($name, $brokers, $props, $topic, $topicProps) {
		$this->producers[$name] = new TopicProducer($brokers, $props, $topic, $topicProps);
	}

	/**
	 * @param string $name 
	 * @return TopicProducer
	 */
	public function getProducer($name) {
		return array_key_exists($name, $this->producers) ? $this->producers[$name] : null;
	}	

	/**
	 * @param string $name
	 * @param $props
	 */
	public function addConsumer($name, $brokers, $props, $topic, $topicProps) {
		$this->consumers[$name] = new TopicConsumer($brokers, $props, $topic, $topicProps);
	}
	
	
	/**
	 * @param $name
	 * @return TopicConsumer
	 */
	public function getConsumer($name) {
		return array_key_exists($name, $this->consumers) ? $this->consumers[$name] : null;
	}
	
}