<?php


namespace common\exceptions;


use RuntimeException;
use Throwable;

class ActiveRecordNotFoundException extends RuntimeException
{
    private string $class;

    public function __construct(string $class, $code = 0, Throwable $previous = null)
    {
        $this->class = $class;
        parent::__construct('Active record model of ' . $class . ' not found', $code, $previous);
    }

    public function getClass(): string
    {
        return $this->class;
    }
}