<?php
declare(strict_types=1);

namespace WernerDweight\RA\Exception;

class RAException extends \Exception implements \Throwable
{
    public const INVALID_OFFSET = 1;

    protected const MESSAGES = [
        self::INVALID_OFFSET => 'Invalid offset "%s"!',
    ];

    /**
     * @param int $code
     * @param string ...$payload
     * @return RAException
     */
    public static function create(int $code, string ...$payload): RAException
    {
        return new self(
            sprintf(self::MESSAGES[$code], ...$payload),
            $code
        );
    }
}
