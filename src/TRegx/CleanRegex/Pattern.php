<?php
namespace TRegx\CleanRegex;

use TRegx\CleanRegex\ForArray\ForArrayPattern;
use TRegx\CleanRegex\ForArray\ForArrayPatternImpl;
use TRegx\CleanRegex\Internal\InternalPattern;
use TRegx\CleanRegex\Internal\Subject;
use TRegx\CleanRegex\Match\MatchPattern;
use TRegx\CleanRegex\Remove\RemoveLimit;
use TRegx\CleanRegex\Remove\RemovePattern;
use TRegx\CleanRegex\Replace\NonReplaced\DefaultStrategy;
use TRegx\CleanRegex\Replace\NonReplaced\ReplacePatternFactory;
use TRegx\CleanRegex\Replace\ReplaceLimit;
use TRegx\CleanRegex\Replace\ReplaceLimitImpl;
use TRegx\CleanRegex\Replace\ReplacePatternImpl;
use TRegx\CleanRegex\Replace\SpecificReplacePatternImpl;
use TRegx\SafeRegex\preg;

class Pattern
{
    /** @var InternalPattern */
    private $pattern;

    private function __construct(InternalPattern $pattern)
    {
        $this->pattern = $pattern;
    }

    public function test(string $subject): bool
    {
        return preg::match($this->pattern->pattern, $subject) === 1;
    }

    public function fails(string $subject): bool
    {
        return preg::match($this->pattern->pattern, $subject) === 0;
    }

    public function match(string $subject): MatchPattern
    {
        return new MatchPattern($this->pattern, $subject);
    }

    public function replace(string $subject): ReplaceLimit
    {
        return new ReplaceLimitImpl(function (int $limit) use ($subject) {
            return new ReplacePatternImpl(
                new SpecificReplacePatternImpl($this->pattern, $subject, $limit, new DefaultStrategy()),
                $this->pattern,
                $subject,
                $limit,
                new ReplacePatternFactory());
        });
    }

    public function remove(string $subject): RemoveLimit
    {
        return new RemoveLimit(function (int $limit) use ($subject) {
            return (new RemovePattern($this->pattern, $subject, $limit))->remove();
        });
    }

    public function forArray(array $haystack): ForArrayPattern
    {
        return new ForArrayPatternImpl($this->pattern, $haystack);
    }

    public function split(string $subject): array
    {
        return (new SplitPattern($this->pattern, $subject))->split();
    }

    public function count(string $subject): int
    {
        return (new CountPattern($this->pattern, new Subject($subject)))->count();
    }

    public function valid(): bool
    {
        return (new ValidPattern($this->pattern->pattern))->isValid();
    }

    public function delimiter(): string
    {
        return $this->pattern->pattern;
    }

    public static function quote(string $pattern): string
    {
        return (new QuotePattern($pattern))->quote();
    }

    public static function unquote(string $pattern): string
    {
        return (new UnquotePattern($pattern))->unquote();
    }

    public static function of(string $pattern, string $flags = ''): Pattern
    {
        return new Pattern(InternalPattern::standard($pattern, $flags));
    }

    /**
     * @param string $delimitedPattern
     * @return Pattern
     * @deprecated Please use method \TRegx\CleanRegex\Pattern::of. Method Pattern::pcre() is only present, in case
     * if there's an automatic delimiters' bug, that would make "Pattern" error-prone.
     * @see \TRegx\CleanRegex\Pattern::of
     */
    public static function pcre(string $delimitedPattern): Pattern
    {
        return new Pattern(InternalPattern::pcre($delimitedPattern));
    }

    public static function prepare(array $input, string $flags = ''): Pattern
    {
        return PatternBuilder::builder()->prepare($input, $flags);
    }

    public static function bind(string $input, array $values, string $flags = ''): Pattern
    {
        return PatternBuilder::builder()->bind($input, $values, $flags);
    }

    public static function inject(string $input, array $values, string $flags = ''): Pattern
    {
        return PatternBuilder::builder()->inject($input, $values, $flags);
    }

    public static function compose(array $patterns): CompositePattern
    {
        return PatternBuilder::compose($patterns);
    }
}
