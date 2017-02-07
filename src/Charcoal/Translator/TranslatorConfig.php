<?php

namespace Charcoal\Translator;

use InvalidArgumentException;

use Charcoal\Config\AbstractConfig;

/**
 *
 */
class TranslatorConfig extends AbstractConfig
{
    /**
     * @var string[]
     */
    private $loaders;

    /**
     * @var string[]
     */
    private $paths;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @return array
     */
    public function defaults()
    {
        return [
            'loaders'   => [
                'csv'
            ],
            'paths'     => [
                'translations/'
            ],
            'debug'     => false,
            'cache_dir' => 'translator_cache'
        ];
    }


    /**
     * @param string[] $loaders The list of active loaders.
     * @return TranslatorConfig Chainable
     */
    public function setLoaders(array $loaders)
    {
        $availableLoaders = [
            'csv',
            'json',
            'mo',
            'php',
            'po',
            'xliff',
            'yaml'
        ];
        $this->loaders = [];
        foreach ($loaders as $loader) {
            if (!in_array($loader, $availableLoaders)) {
                throw new InvalidArgumentException(
                    sprintf('Loader "%s" is not a valid loader.', $loader)
                );
            }
            $this->loaders[] = $loader;
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function loaders()
    {
        return $this->loaders;
    }

    /**
     * @param string[] $paths
     * @throws InvalidArgumentException if the path is not a string.
     * @return TranslatorConfig Chainable
     */
    public function setPaths(array $paths)
    {
        $this->paths = [];
        foreach ($paths as $path) {
            if (!is_string($path)) {
                throw new InvalidArgumentException(
                    'Translator path must be a string'
                );
            }
            $this->paths[] = $path;
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function paths()
    {
        return $this->paths;
    }

    /**
     * @param boolean $debug
     * @return TranslatorConfig Chainable
     */
    public function setDebug($debug)
    {
        $this->debug = !!$debug;
        return $this;
    }

    /**
     * @return boolean
     */
    public function debug()
    {
        return $this->debug;
    }

    /**
     * @param string $cacheDir The cache directory.
     * @throws InvalidArgumentException If the cache dir argument is not a string.
     * @return TranslatorConfig Chainable
     */
    public function setCacheDir($cacheDir)
    {
        if (!is_string($cacheDir)) {
            throw new InvalidArgumentException(
                'Cache dir must be a string'
            );
        }
        $this->cacheDir = $cacheDir;
        return $this;
    }

    /**
     * @return string
     */
    public function cacheDir()
    {
        return $this->cacheDir;
    }
}
