<?php
namespace Test\Unit\TRegx\CleanRegex\Exception\CleanRegex;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Exception\CleanRegex\InvalidReplacementException;

class InvalidReplacementExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetMessageWithType()
    {
        // given
        $exception = new InvalidReplacementException(true);

        // when
        $actualMessage = $exception->getMessage();

        // then
        $this->assertEquals('Invalid callback() callback return type. Expected string, but boolean (true) given', $actualMessage);
    }
}
