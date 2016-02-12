<?php

namespace Ephemeral;

use Symfony\Component\Finder\Finder;

class Config {
    protected $directory;
    protected $config = [];

    public function __construct($directory) {
        $this->directory = $directory;
        foreach ($files = $this->getConfigFiles() as $key => $path) {
            $this->set($key, require $path);
        }
    }

    public function set($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                $this->setConfig($this->config, $innerKey, $innerValue);
            }
        }else {
            $this->setConfig($this->config, $key, $value);
        }
    }

    public function setConfig(&$array, $key, $value) {
        if (is_null($key)) {
            return $array = $value;
        }
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }
        }

        $array[array_shift($keys)] = $value;
        return $array;
    }

    public function all() {
        return $this->config;
    }

    public function get($key, $default = null) {
        $array = $this->config;
        if (is_null($key)) {
            return $array;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if ((! is_array($array) || ! array_key_exists($segment, $array))) {
                return $default;
            }
            $array = $array[$segment];
        }
        return $array;
    }


    private function getConfigFiles() {
        $files = [];
        $configPath = realpath($this->directory);
        foreach(Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $nesting = $this->getConfigurationNesting($file, $configPath);
            $files[$nesting.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }
        return $files;
    }

    protected function getConfigurationNesting($file, $configPath)
    {
        $directory = dirname($file->getRealPath());
        if ($tree = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree).'.';
        }
        return $tree;
    }

}