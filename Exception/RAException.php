<?php
declare(strict_types=1);

namespace WernerDweight\RA\Exception;

final class RAException extends \Exception implements \Throwable
{
    /** @var int */
    public const INVALID_OFFSET = 1;
    /** @var int */
    public const INVALID_INCREMENT_TYPE = 2;

    /** @var string[] */
    private const MESSAGES = [
        self::INVALID_OFFSET => 'Invalid offset "%s"!',
        self::INVALID_INCREMENT_TYPE => 'Only integers and floats can be incremented/decremented!',
    ];

    /**
     * @param int    $code
     * @param string ...$payload
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function __construct(int $code, string ...$payload)
    {
        parent::__construct(\Safe\sprintf(self::MESSAGES[$code], ...$payload), $code);
    }
}
