<?php
namespace Test\Unit\CleanRegex\Match\MatchPattern\groups\first;

use CleanRegex\Exception\CleanRegex\NonexistentGroupException;
use CleanRegex\Internal\Pattern;
use CleanRegex\Match\MatchPattern;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MatchPatternTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetGroups()
    {
        // given
        $pattern = new MatchPattern(new Pattern('(?<two>[A-Z][a-z])?(?<rest>[a-z]+)'), 'Nice Matching Pattern');

        // when
        $twoGroups = $pattern->group('two')->first();
        $restGroups = $pattern->group('rest')->first();

        // then
        $this->assertEquals('Ni', $twoGroups);
        $this->assertEquals('ce', $restGroups);
    }

    /**
     * @test
     */
    public function shouldReturnUnmatchedGroups()
    {
        // given
        $pattern = new MatchPattern(new Pattern('(?<hour>\d\d)?:(?<minute>\d\d)?'), 'First->11:__   Second->__:12   Third->13:32');

        // when
        $hours = $pattern->group('hour')->first();
        $minutes = $pattern->group('minute')->first();

        // then
        $this->assertEquals('11', $hours);
        $this->assertEquals(null, $minutes);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArray_onNotMatchedSubject()
    {
        // given
        $pattern = new MatchPattern(new Pattern('(?<two>[A-Z][a-z])?(?<rest>[a-z]+)'), 'NOT MATCHING');

        // when
        $groups = $pattern->group('two')->first();

        // then
        $this->assertEquals(null, $groups);
    }

    /**
     * @test
     */
    public function shouldReturnNull_onNonExistentGroup()
    {
        // given
        $pattern = new MatchPattern(new Pattern('(?<existing>[a-z]+)'), 'matching');

        // when
        $first = $pattern->group('missing')->first();

        // then
        $this->assertNull($first);
    }

    /**
     * @test
     */
    public function shouldThrow_onNonExistentGroup()
    {
        $this->markTestIncomplete("first() should throw on missing group, but preg_match() is fucked up and doesn't return unmatched groups");
        // given
        $pattern = new MatchPattern(new Pattern('(?<existing>[a-z]+)'), 'matching');

        // then
        $this->expectException(NonexistentGroupException::class);
        $this->expectExceptionMessage('Nonexistent group: missing');

        // when
        $pattern->group('missing')->first();
    }
}