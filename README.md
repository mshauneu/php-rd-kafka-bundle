# KafkaBundle

## About 
This [Symfony](https://symfony.com) bundle provides connectivity to the [Kafka](http://kafka.apache.org) publish-subscribe messaging system based on [rdkafka](https://github.com/arnaud-lb/php-rdkafka) binding to [librdkafka](https://github.com/edenhill/librdkafka)

## Installation
Add the dependency in your composer.json
```json
{
    "require": {
        "mshauneu/php-rdkafka-bundle"
    }
}
```
Enable the bundle in your application kernel
```php
// app/AppKernel.php
public function registerBundles() {
    $bundles = array(
        // ...
        new Mshauneu\RdKafkaBundle\MshauneuRdKafkaBundle(),
    );
}
```
## Configuration
Simple configuration could look like:
```yaml
mshauneu_rd_kafka:
  producers: 
    test_producer: 
      brokers: 127.0.0.1:9092
      topic: test_topic   
  consumers:
    test_consumer:
      brokers: 127.0.0.1:9092
      topic: test_topic   
      properties: 
        group_id: "test_group_id"
      topic_properties: 
        offset_store_method: broker           
        auto_offset_reset: smallest
        auto_commit_interval_ms: 100
```
Configuration properties are documented:
- for producer or  consumer in [CommunicatorConfiguration.php](https://github.com/mshauneu/php-rd-kafka-bundle/blob/master/src/Mshauneu/RdKafkaBundle/DependencyInjection/CommunicatorConfiguration.php)
- for topic to produce in [TopicProducerConfiguration.php](https://github.com/mshauneu/php-rd-kafka-bundle/blob/master/src/Mshauneu/RdKafkaBundle/DependencyInjection/TopicProducerConfiguration.php)
- for topic to consume in [TopicConsumerConfiguration.php](https://github.com/mshauneu/php-rd-kafka-bundle/blob/master/src/Mshauneu/RdKafkaBundle/DependencyInjection/TopicConsumerConfiguration.php)

## Usage
### Publishing messages to a Kafka topic
From a Symfony controller:
```php
$payload = 'test_message';
$topicProducer = $container->get('mshauneu_rd_kafka')->getProducer("test_producer");
$topicProducer->produceStart();
$topicProducer->produce("message");
$topicProducer->produceStop();
``` 
By CLI:
```bash
./app/console kafka:producer --producer test_producer test_message 
```

### Consume messages out of a Kafka topic:
Implement [ConsumerInterface](https://github.com/mshauneu/php-rd-kafka-bundle/blob/master/src/Mshauneu/RdKafkaBundle/Topic/ConsumerInterface.php)
```php
class MessageHandler implements ConsumerInterface {
	public function consume($topic, $partition, $offset, $key, $payload) {
		echo "Received payload: " . $payload . PHP_EOL;
	}
}
```
Register it: 
```yaml
test_message_handler:
    class: MessageHandler
```
From a Symfony controller:
```php
$topicConsumer = $container->get('mshauneu_rd_kafka')->getConsumer("test_producer");
$topicConsumer->consumeStart(TopicCommunicator::OFFSET_STORED);
$topicConsumer->consume($consumerImpl);
$topicConsumer->consumeStop();
```
By CLI:
```bash
./app/console kafka:consumer --consumer test_consumer --handler test_message_handler 
```

## License

This project is under the MIT License. See the [LICENSE](https://github.com/mshauneu/php-rd-kafka-bundle/blob/master/LICENSE) file for the full license text.

