<?php

namespace Mshauneu\RdKafkaBundle\Tests\DependencyInjection;

use Mshauneu\RdKafkaBundle\DependencyInjection\MshauneuRdKafkaExtension;
use Mshauneu\RdKafkaBundle\Topic\ConsumerInterface;
use Mshauneu\RdKafkaBundle\Topic\Manager;
use Mshauneu\RdKafkaBundle\Topic\TopicCommunicator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * MshauneuRdKafkaExtension Test
 * 
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
class MsRdKafkaExtensionTest extends \PHPUnit_Framework_TestCase {
	
	function testDefinition() {
		$container = $this->getContainer('test.yml');
		$this->assertTrue($container->has('mshauneu_rd_kafka'));
		$manager = $container->get('mshauneu_rd_kafka');

		$topicProducer = $manager->getProducer("test_producer");
		$topicProducer->produceStart();
		$topicProducer->produce("message");
		$topicProducer->produceStop();
		
		$consumerImpl = new ConsumerImpl();
		$topicConsumer = $manager->getConsumer("test_consumer");
		$topicConsumer->consumeStart(TopicCommunicator::OFFSET_STORED);
		$topicConsumer->consume($consumerImpl);
		$topicConsumer->consumeStop();
	}
	
	private function getContainer($file, $debug = false) {
		$container = new ContainerBuilder(new ParameterBag(array('kernel.debug' => $debug)));
		$container->registerExtension(new MshauneuRdKafkaExtension());
		$locator = new FileLocator(__DIR__.'/Fixtures');
		$loader = new YamlFileLoader($container, $locator);
		$loader->load($file);
		$container->getCompilerPassConfig()->setOptimizationPasses(array());
		$container->getCompilerPassConfig()->setRemovingPasses(array());
		$container->compile();
		return $container;
	}
	
}

class ConsumerImpl implements ConsumerInterface {
	
	public function consume($topic, $partition, $offset, $key, $payload) {
		fwrite(STDOUT, "Received payload: " . var_export($payload, true) . PHP_EOL);
	}

}