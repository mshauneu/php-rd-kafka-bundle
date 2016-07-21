<?php

namespace Mshauneu\RdKafkaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Extension
 *
 * @author Mike Shauneu <mike.shauneu@gmail.com>
 */
class MshauneuRdKafkaExtension extends Extension {
	
	/**
	 * {@inheritdoc}
	 * @see \Symfony\Component\DependencyInjection\Extension\ExtensionInterface::load()
	 */
	public function load(array $configs, ContainerBuilder $container) {
		$configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $def = $container->getDefinition('mshauneu_rd_kafka');
        
        if (array_key_exists('producers', $config) && is_array($config['producers'])) {
        	foreach ($config['producers'] as $producerName => $producerConfig) {
        		$brokers = $producerConfig["brokers"];
        		$topic = $producerConfig["topic"];
        		$props = array_key_exists("properties", $producerConfig) ? $producerConfig["properties"] : null;
        		$topicProps = array_key_exists("topic_properties", $producerConfig) ? $producerConfig["topic_properties"] : null;
        		$def->addMethodCall('addProducer', [$producerName, $brokers, $props, $topic, $topicProps]);
        	}
        }
        
        if (array_key_exists('consumers', $config) && is_array($config['consumers'])) {
        	foreach ($config['consumers'] as $consumerName => $consumerConfig) {
        		$brokers = $consumerConfig["brokers"];
        		$topic = $consumerConfig["topic"];
        		$props = array_key_exists("properties", $consumerConfig) ? $consumerConfig["properties"] : null;
        		$topicProps = array_key_exists("topic_properties", $consumerConfig) ? $consumerConfig["topic_properties"] : null;
        		$def->addMethodCall('addConsumer', [$consumerName, $brokers, $props, $topic, $topicProps]);
        	}
        }
        
	}
	
}