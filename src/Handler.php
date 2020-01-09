<?php
declare(strict_types=1);

namespace Consumer;

use RdKafka\Exception;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\TopicPartition;
use Throwable;

class Handler
{
    /**
     * @var KafkaConsumer
     */
    private $kafkaConsumer;

    /**
     * @var ConsumerInterface
     */
    private $consumer;

    /**
     * @var array
     */
    private $partitions;

    /**
     * Handler constructor.
     * @param KafkaConsumer $kafkaConsumer
     * @param ConsumerInterface $consumer
     * @param array<int> $partitions
     * @throws \Exception
     */
    public function __construct(
        KafkaConsumer $kafkaConsumer,
        ConsumerInterface $consumer,
        array $partitions
    ) {
        $this->kafkaConsumer = $kafkaConsumer;
        $this->consumer = $consumer;
        $this->partitions = $partitions;
    }

    /**
     * @return void
     * @throws Exception
     * @throws ConsumerNotSatisfiableException
     */
    public function handle(): void
    {
        $this->kafkaConsumer->subscribe([$this->consumer::getTopic()]);
        $this->kafkaConsumer->assign($this->getTopicPartitions());

        while (true) {
            $message = $this->kafkaConsumer->consume(-1);

            if ($message->err !== RD_KAFKA_RESP_ERR_NO_ERROR) {
                continue;
            }

            $this->doHandle($message);
        }
    }

    /**
     * @param array<TopicPartition>
     * @return array
     */
    private function getTopicPartitions(): array
    {
        return array_map(
            function ($partition) {
                return new TopicPartition($this->consumer::getTopic(), $partition);
            },
            $this->partitions
        );
    }

    /**
     * @param Message $message
     * @return void
     * @throws ConsumerNotSatisfiableException
     */
    private function doHandle(Message $message): void
    {
        $payload = json_decode($message->payload);
        $this->validateProps((array)$payload);

        try {
            $this->consumer->handle($payload);
            $this->kafkaConsumer->commit();
        } catch (Throwable $exception) {
            $this->consumer->handleException($exception);
        }
    }

    /**
     * @param array $message
     * @return void
     * @throws ConsumerNotSatisfiableException
     */
    private function validateProps(array $message): void
    {
        $providedProps = array_keys($message);

        if (array_diff($this->consumer::getProps(), $providedProps)) {
            throw new ConsumerNotSatisfiableException(
                "Consumer is not satisfiable.",
                $this->consumer::getProps(),
                $providedProps
            );
        }
    }
}
