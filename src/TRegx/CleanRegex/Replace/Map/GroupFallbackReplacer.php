<?php
namespace TRegx\CleanRegex\Replace\Map;

use TRegx\CleanRegex\Exception\CleanRegex\GroupNotMatchedException;
use TRegx\CleanRegex\Exception\CleanRegex\InternalCleanRegexException;
use TRegx\CleanRegex\Exception\CleanRegex\NonexistentGroupException;
use TRegx\CleanRegex\Internal\InternalPattern as Pattern;
use TRegx\CleanRegex\Internal\Match\Base\Base;
use TRegx\CleanRegex\Internal\Subjectable;
use TRegx\CleanRegex\Replace\NonReplaced\NonReplacedStrategy;
use TRegx\SafeRegex\preg;
use function array_key_exists;

class GroupFallbackReplacer
{
    /** @var Pattern */
    private $pattern;
    /** @var Subjectable */
    private $subject;
    /** @var int */
    private $limit;
    /** @var NonReplacedStrategy */
    private $strategy;
    /** @var Base */
    private $base;
    /** @var int */
    private $counter = -1;

    public function __construct(Pattern $pattern, Subjectable $subject, int $limit, NonReplacedStrategy $strategy, Base $base)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->limit = $limit;
        $this->strategy = $strategy;
        $this->base = $base;
    }

    public function replaceOrFallback($nameOrIndex, NonReplacedStrategy $strategy, callable $unexpectedReplacementHandler): string
    {
        $this->counter = -1;
        return $this->replaceUsingCallback(function (array $match) use ($nameOrIndex, $strategy, $unexpectedReplacementHandler) {
            $this->counter++;
            $this->validateGroup($match, $nameOrIndex);
            return $this->getReplacementOrHandle($match, $nameOrIndex, $strategy, $unexpectedReplacementHandler);
        });
    }

    private function validateGroup(array $match, $nameOrIndex): void
    {
        if (!array_key_exists($nameOrIndex, $match)) {
            $matches = $this->base->matchAllOffsets();
            if (!$matches->hasGroup($nameOrIndex)) {
                throw new NonexistentGroupException($nameOrIndex);
            }
        }
    }

    private function replaceUsingCallback(callable $closure): string
    {
        $result = $this->pregReplaceCallback($closure, $replaced);
        if ($replaced === 0) {
            return $this->strategy->replacementResult($this->subject->getSubject()) ?? $result;
        }
        return $result;
    }

    private function pregReplaceCallback(callable $closure, ?int &$replaced): string
    {
        return preg::replace_callback(
            $this->pattern->pattern,
            $closure,
            $this->subject->getSubject(),
            $this->limit,
            $replaced);
    }

    private function getReplacementOrHandle(array $match, $nameOrIndex, NonReplacedStrategy $strategy, callable $unexpectedReplacementHandler): string
    {
        $occurrence = $match[$nameOrIndex];
        if ($occurrence === null) {
            throw GroupNotMatchedException::forReplacement($this->subject, $nameOrIndex);
        }
        if ($occurrence === '') {
            // With preg_replace_callback - it's impossible to distinguish unmatched group from a matched empty string
            $matches = $this->base->matchAllOffsets();
            if (!$matches->hasGroup($this->counter)) {
                throw new InternalCleanRegexException();
            }
            if (!$matches->isGroupMatched($nameOrIndex, $this->counter)) {
                throw GroupNotMatchedException::forReplacement($this->subject, $nameOrIndex);
            }
        }
        return $strategy->replacementResult($occurrence) ?? $unexpectedReplacementHandler($match[0], $occurrence);
    }
}