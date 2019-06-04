<?php
namespace TRegx\CleanRegex\Replace\NonReplaced\Map\Exception;

class GroupMessageExceptionStrategy implements MissingReplacementExceptionMessageStrategy
{
    public function create(string $value, $nameOrIndex, string $group): MissingReplacementKeyException
    {
        return MissingReplacementKeyException::forGroup($value, $nameOrIndex, $group);
    }
}