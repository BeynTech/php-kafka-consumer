<?php
declare(strict_types=1);

namespace Consumer;

use Throwable;

interface ConsumerInterface
{
    /**
     * @return array<string>
     * @return array
     */
    public static function getProps(): array;

    /**
     * @return string
     * @return string
     */
    public static function getTopic(): string;

    /**
     * @param $message
     * @return void
     */
    public function handle($message): void;

    /**
     * @param Throwable $exception
     * @return void
     */
    public function handleException(Throwable $exception): void;
}
