<?php

namespace eLife\IsolatedDrupalBehatExtension;

final class Drupal
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $profile;

    /**
     * @var string
     */
    private $site;

    /**
     * @param string $path
     * @param string $uri
     * @param string $profile
     */
    public function __construct($path, $uri, $profile)
    {
        $this->path = rtrim($path, '/');
        $this->uri = rtrim($uri, '/') . '/';
        $this->profile = $profile;
        $this->site = $this->uriToDirectoryName($this->uri);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getSitePath()
    {
        return $this->path . '/' . $this->getLocalPath();
    }

    /**
     * @return string
     */
    public function getLocalPath()
    {
        return 'sites/' . $this->site;
    }

    /**
     * @return string
     */
    public function getSiteDir()
    {
        return $this->site;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    private function uriToDirectoryName($uri)
    {
        $parts = parse_url($uri);

        $return = [];

        if (!empty($parts['port']) && 80 !== $parts['port']) {
            $return[] = $parts['port'];
        }
        $return[] = $parts['host'];
        if (!empty($parts['path'])) {
            $return[] = str_replace('/', '.', trim($parts['path'], '/'));
        }

        return implode('.', array_filter($return));
    }
}
