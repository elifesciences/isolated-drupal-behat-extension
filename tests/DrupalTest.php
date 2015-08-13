<?php

namespace eLife\IsolatedDrupalBehatExtension;

final class DrupalTest extends TestCase
{
    /**
     * @test
     * @dataProvider pathProvider
     */
    public function itHasAPath($path, $expected)
    {
        $drupal = new Drupal($path, 'http://localhost/', 'standard');

        $this->assertSame($expected, $drupal->getPath());
    }

    public function pathProvider()
    {
        return [
            ['/foo/bar', '/foo/bar'],
            ['/foo/bar/', '/foo/bar'],
        ];
    }

    /**
     * @test
     * @dataProvider uriProvider
     */
    public function itHasAUri($uri, $expected)
    {
        $drupal = new Drupal('/foo/bar', $uri, 'standard');

        $this->assertSame($expected, $drupal->getUri());
    }

    public function uriProvider()
    {
        return [
            ['http://localhost', 'http://localhost/'],
            ['http://localhost/', 'http://localhost/'],
            ['http://localhost/foo', 'http://localhost/foo/'],
            ['http://localhost/foo/', 'http://localhost/foo/'],
        ];
    }

    /**
     * @test
     * @dataProvider sitePathProvider
     */
    public function itHasASitePath($uri, $expected)
    {
        $drupal = new Drupal('/foo/bar', $uri, 'standard');

        $this->assertSame(
            '/foo/bar/sites/' . $expected,
            $drupal->getSitePath()
        );
        $this->assertSame(
            'sites/' . $expected,
            $drupal->getLocalPath()
        );
    }

    public function sitePathProvider()
    {
        return [
            ['http://localhost', 'localhost'],
            ['http://localhost/', 'localhost'],
            ['http://localhost/foo', 'localhost.foo'],
            ['http://localhost/foo/', 'localhost.foo'],
            ['http://foo.bar:1234/baz/qux?#', '1234.foo.bar.baz.qux'],
        ];
    }

    /**
     * @test
     */
    public function itHasAProfile()
    {
        $drupal = new Drupal('/foo/bar', 'http://localhost/', 'standard');

        $this->assertSame('standard', $drupal->getProfile());
    }
}
