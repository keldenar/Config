<?php

namespace Ephemeral\Providers;

use Ephemeral\Config;
use Silex\ServiceProviderInterface;
use Silex\Application;

class ConfigServiceProvider implements ServiceProviderInterface {

    private $dirname;

    public function __construct($dirname) {
        $this->dirname = $dirname;
    }

    public function register(Application $app)
    {
        // TODO: Implement register() method.
        $dir = $this->dirname;
        $app['config'] = $app->share(function () use ($dir) {
            return new Config($dir);
        });
    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}