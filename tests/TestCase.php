<?php

namespace Appointer\AuthyApi\Test;

use Appointer\AuthyApi\AuthyApiServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [AuthyApiServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('authy-api.api-base-uri', 'https://sandbox-api.authy.com/protected/json/');
        $app['config']->set('authy-api.api-key', 'bf12974d70818a08199d17d5e2bae630');
    }
}