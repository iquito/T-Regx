<?php
namespace TRegx\CleanRegex\Exception\CleanRegex;

class IntegerFormatException extends \Exception
{
    public static function forGroup($nameOrIndex, string $value): IntegerFormatException
    {
        return new self("Expected to parse group '$nameOrIndex', but '$value' is not a valid integer");
    }

    public static function forMatch(string $value): IntegerFormatException
    {
        return new self("Expected to parse '$value', but it is not a valid integer");
    }
}
