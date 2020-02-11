<?php

use Consumer\ConsumerInterface;
use Consumer\ConsumerNotSatisfiableException;
use Consumer\Handler;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;

require __DIR__ . "/../vendor/autoload.php";

$conf = new Conf();

$conf->set("metadata.broker.list", "127.0.0.1:9092");
$conf->set("group.id", "consumer-group-1");

$kafkaConsumer = new KafkaConsumer($conf);
$consumer = new class implements ConsumerInterface {

    public static function getFormat(): string
    {
        return \Consumer\MessageFormat::JSON;
    }

    /** @inheritDoc */
    public static function getProps(): array
    {
        return [
            "ticket",
            "user",
        ];
    }

    /** @inheritDoc */
    public static function getTopic(): string
    {
        return "my.topic";
    }

    /** @inheritDoc */
    public function handle($message): void
    {
        var_dump($message);
    }

    /** @inheritDoc */
    public function handleException(Throwable $exception): void
    {
        // TODO: Implement handleException() method.
    }
};
$partitions = [0, 1, 2];

try {
    $handler = new Handler($kafkaConsumer, $consumer, $partitions);
    $handler->handle();
} catch (ConsumerNotSatisfiableException $e) {
    echo "Consumer is not satisfiable\n";
    printf("%s required, %s provided\n", json_encode($e->getRequiredProps()), json_encode($e->getProviedProps()));

    exit(1);
} catch (Exception $e) {
    echo "An error occured\n";
    echo $e->getMessage(), "\n";

    exit(1);
};
