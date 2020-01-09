<?php
declare(strict_types=1);

namespace Consumer;

use Exception;
use Throwable;

class ConsumerNotSatisfiableException extends Exception
{
    /**
     * @var array
     */
    private $requiredProps;

    /**
     * @var array
     */
    private $providedProps;

    public function __construct(
        string $message,
        array $requiredProps,
        array $providedProps,
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->requiredProps = $requiredProps;
        $this->providedProps = $providedProps;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getRequiredProps(): array
    {
        return $this->requiredProps;
    }

    /**
     * @return array
     */
    public function getProviedProps(): array
    {
        return $this->providedProps;
    }
}
