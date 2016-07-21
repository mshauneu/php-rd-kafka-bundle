<?php

namespace Mshauneu\RdKafkaBundle\Topic;

/**
 * ConsumerInterface
 *
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
interface ConsumerInterface {
	
	/**
	 * Consume
	 * 
	 * @param string $topic Topic name
	 * @param int $partition Partition
	 * @param int $offset Message offset
	 * @param string $key Optional message key
	 * @param string $payload Message payload
	 * @return mixed
	 */
	public function consume($topic, $partition, $offset, $key, $payload);
	
}