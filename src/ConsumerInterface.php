<?php
declare(strict_types=1);

namespace Consumer;

use Throwable;

interface ConsumerInterface
{
    /**
     * @return string
     */
    public static function getFormat(): string;

    /**
     * @return string
     */
    public static function getTopic(): string;

    /**
     * @return array
     */
    public static function getProps(): array;

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
