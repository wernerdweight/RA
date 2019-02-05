<?php
declare(strict_types=1);

namespace WernerDweight\RA\Exception;

final class RAException extends \Exception implements \Throwable
{
    /** @var int */
    public const INVALID_OFFSET = 1;
    /** @var int */
    public const INVALID_NUMBER_OF_ELEMENTS = 2;

    /** @var string[] */
    protected const MESSAGES = [
        self::INVALID_OFFSET => 'Invalid offset "%s"!',
        self::INVALID_NUMBER_OF_ELEMENTS => 'Invalid number of elements!',
    ];

    /**
     * @param int    $code
     * @param string ...$payload
     *
     * @return RAException
     */
    public static function create(int $code, string ...$payload): self
    {
        return new self(sprintf(self::MESSAGES[$code], ...$payload), $code);
    }
}
