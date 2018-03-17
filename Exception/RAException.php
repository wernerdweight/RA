<?php

namespace WernerDweight\RA\Exception;

class RAException extends \Exception implements \Throwable
{
    public const INVALID_OFFSET = 'invalid-offset';

    protected const MESSAGES = [
        self::INVALID_OFFSET => 'Invalid offset %s!',
    ];

    /**
     * @param string $code
     * @param string[] $payload
     * @return RAException
     */
    public static function create(string $code, string ...$payload): RAException
    {
        return new self(
            sprintf(self::MESSAGES[$code], ...$payload),
            $code
        );
    }
}
