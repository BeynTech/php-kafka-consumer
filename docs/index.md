# Kafka consumer

## Implementing ConsumerInterface

- `getProps` returns required props for running.
- `getTopic` returns topic name.
- `getFormat` returns message format.
- `handle` main handler.
- `handleException` runs when `handle` throws exception.

**!! WARNING: Due to limitations in metric names, topics with a period ('.') or underscore ('_') could collide.
To avoid issues it is best to use either, but not both.**

```php
<?php

final class UserCreatedConsumer implements \Consumer\ConsumerInterface
{
    public static function getProps(): array
    {
        return [];
    }
    
    public static function getTopic(): string
    { 
        return "userCreated";
    }
 
    public static function getFormat(): string
    {
        return \Consumer\MessageFormat::JSON;
    }
    
    public function handle($message): void
    {
        var_dump($message);
    }
    
    public function handleException(Throwable $exception): void
    {
        // ...
    }
}
```

## Running Handler

### __construct

**Arguments**

- `RdKafka\KafkaConsumer`
- `\Consumer\ConsumerInterface`
- `array<int>`

### handle
This method runs the main logic.

**Returns**

- `void`

**Throws**

- `\RdKafka\Exception`
- `\Consumer\ConsumerNotSatisfiableException`

**Example**
```php
<?php

$conf = new \RdKafka\Conf();

$producer = new \RdKafka\KafkaConsumer($conf);

/** @var \Consumer\ConsumerInterface $consumer */
$consumer = new UserCreatedConsumer();

$partitions = [0, 1, 2];

$handler = new \Consumer\Handler($producer, $consumer, $partitions);

$handler->handle();
```