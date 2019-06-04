<?php
namespace TRegx\CleanRegex\Replace\NonReplaced\Map\Exception;

use TRegx\CleanRegex\Exception\CleanRegex\CleanRegexException;

class MissingReplacementKeyException extends CleanRegexException
{
    public static function create(string $value)
    {
        return new self("Expected to replace value '$value', but such key is not found in replacement map.");
    }

    public static function forGroup(string $value, $nameOrIndex, string $group)
    {
        return new self("Expected to replace value '$value' by group '$nameOrIndex' ('$group'), but such key is not found in replacement map.");
    }
}