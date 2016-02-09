<?php

namespace Ephemeral;

class Config {
    protected $directory;
    protected $config = [];

    public function __construct($directory) {
        $this->directory = $directory;
        foreach ($files = $this->getConfigFiles() as $key => $path) {
            $this->set($key, require $path);
        }
    }

    public function set($key, $values = null) {
        if (is_array($values)) {
            foreach ($values as $config_key=>$config_value) {
                $config_key = sprintf("%s.%s", $key, $config_key);
                $this->config[$config_key] = $config_value;
            }
        }
    }

    public function all() {
        return $this->config;
    }

    public function get($variable) {
        list($file, $value) = explode(".", $variable, 2);
        if (($file == null) || ($value == null)) {
            $this->abort(500, "Expected field as file.entry");
        }
        if (array_key_exists($variable, $this->config)) {
            return $this->config[$variable];
        }
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