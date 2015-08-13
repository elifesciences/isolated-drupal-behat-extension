<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

final class LimitingRandomStringGeneratorTest extends RandomStringGeneratorTest
{
    /**
     * @test
     * @dataProvider limitProvider
     */
    public function itWillLimitStrings($limit, $string)
    {
        $this->assertSame(
            $string,
            $this->getGenerator($limit, 'foobar')->generate()
        );
    }

    public function limitProvider()
    {
        return [
            [1, 'f'],
            [3, 'foo'],
            [6, 'foobar'],
            [10, 'foobar'],
        ];
    }

    public function getGenerator($limit = 3, $string = 'foo')
    {
        return new LimitingRandomStringGenerator(
            new StaticStringGenerator($string),
            $limit
        );
    }
}
