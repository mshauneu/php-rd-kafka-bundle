<?php

namespace Mshauneu\RdKafkaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;
use Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;

/**
 * TopicConsumerConfiguration
 *
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
trait TopicConsumerConfiguration {

	/**
	 * If true, periodically commit offset of the last message handed to the application. 
	 * This committed offset will be used when the process restarts to pick up where it left off. 
	 * If false, the application will have to call `rd_kafka_offset_store()` to store an offset (optional).
	 * NOTE: This property should only be used with the simple legacy consumer, when using the high-level 
	 * KafkaConsumer the global `auto.commit.enable` property must be used instead. 
	 * NOTE: There is currently no zookeeper integration, offsets will be written to broker or local file 
	 * according to offset.store.method.
	 * Default value: true 
	 * 
	 * @return \Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition
	 */
	private function autoCommitEnableNodeDef() {
		$node = new BooleanNodeDefinition('auto_commit_enable');
		return $node;
	}
	
	/**
	 * The frequency in milliseconds that the consumer offsets are committed (written) to offset storage.
	 * Default value: 60000
	 * 
	 * @return \Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition
	 */
	private function autoCommitIntervalMsNodeDef() {
		$node = new IntegerNodeDefinition('auto_commit_interval_ms');
		$node->min(10)->max(86400000);
		return $node;
	}
	
	/**
	 * Action to take when there is no initial offset in offset store or the desired offset is out of 
	 * range: 'smallest','earliest' - automatically reset the offset to the smallest offset, 'largest',
	 * 'latest' - automatically reset the offset to the largest offset, 
	 * 'error' - trigger an error which is retrieved by consuming messages and checking 'message->err'.
	 * Default value: largest 
	 * 
	 * @return \Symfony\Component\Config\Definition\Builder\EnumNodeDefinition
	 */
	private function autoOffsetResetNodeDef() {
		$node = new EnumNodeDefinition('auto_offset_reset');
		$node->values(array('smallest', 'earliest', 'largest', 'latest', 'error'));
		return $node;
	}
	
	/**
	 * Path to local file for storing offsets. If the path is a directory a filename will be automatically 
	 * generated in that directory based on the topic and partition.
	 * Default value: .
	 * 
	 * @return \Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition
	 */
	private function offsetStorePathNodeDef() {
		$node = new ScalarNodeDefinition('offset_store_path');
		return $node;
	}
	
	/**
	 * fsync() interval for the offset file, in milliseconds. Use -1 to disable syncing, and 0 for 
	 * immediate sync after each write.
	 * Default value: -1
	 * 
	 * @return \Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition
	 */
	private function offsetStoreSyncIntervalMsNodeDef() {
		$node = new IntegerNodeDefinition('offset_store_sync_interval_ms');
		$node->min(-1)->max(86400000);
		return $node;
	}
	
	/**
	 * Offset commit store method: 
	 * 'file' - local file store (offset.store.path, et.al), 
	 * 'broker' - broker commit store (requires "group.id" to be configured and Apache Kafka 0.8.2 or later on the broker).
	 * Default value: broker 
	 * 
	 * @return \Symfony\Component\Config\Definition\Builder\EnumNodeDefinition
	 */
	private function offsetStoreMethodNodeDef() {
		$node = new EnumNodeDefinition('offset_store_method');
		$node->values(array('file', 'broker'));
		return $node;
	}
	
	/**
	 * Maximum number of messages to dispatch (0 = unlimited)
	 * Default value: 0 
	 * 
	 * @return \Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition
	 */
	private function consumeCallbackMaxMessagesNodeDef() {
		$node = new IntegerNodeDefinition('consume_callback_max_messages');
		$node->min(-1)->max(1000000);
		return $node;
	}

	protected function getTopicConsumerPropertiesNodeDef() {
	    $node = new ArrayNodeDefinition('topic_properties');
		return $node
	      ->canBeUnset()
	      ->children()
	          ->append($this->autoCommitEnableNodeDef())
	          ->append($this->autoCommitIntervalMsNodeDef())
	          ->append($this->autoOffsetResetNodeDef())
	          ->append($this->offsetStorePathNodeDef())
	          ->append($this->offsetStoreSyncIntervalMsNodeDef())
	          ->append($this->offsetStoreMethodNodeDef())
	          ->append($this->consumeCallbackMaxMessagesNodeDef())
	      ->end()
	    ;
  	}
	
}