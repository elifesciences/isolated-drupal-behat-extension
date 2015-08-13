<?php

namespace eLife\IsolatedDrupalBehatExtension\RandomString;

final class HashRandomStringGeneratorTest extends RandomStringGeneratorTest
{
    /**
     * @var HashRandomStringGenerator
     */
    private $generator;

    public function setUp()
    {
        $this->generator = new HashRandomStringGenerator();
    }

    /**
     * @test
     * @dataProvider hashAlgorithmProvider
     */
    public function itCanBeDifferentHashAlgorithms($algorithm, $expectedLength)
    {
        $generator = new HashRandomStringGenerator($algorithm);

        $this->assertRegExp(
            '/^[a-z0-9]{' . $expectedLength . '}$/',
            $generator->generate()
        );
    }

    public function hashAlgorithmProvider()
    {
        return [
            ['crc32', 8],
            ['md5', 32],
            ['sha256', 64],
        ];
    }

    public function getGenerator()
    {
        return $this->generator;
    }
}
