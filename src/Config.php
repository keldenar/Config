<?php

namespace Ephemeral;

class Config {
    protected $directory;

    public function __construct($directory) {
        $this->directory = $directory;
    }

    public function get($variable) {
        // expected format foo.bar
        // foo == file
        // bar == value
        list($file, $value) = explode(".", $variable, 2);
    }
}