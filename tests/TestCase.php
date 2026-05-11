<?php

namespace Zizaco\Entrust\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Zizaco\Entrust\EntrustServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            EntrustServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Entrust' => \Zizaco\Entrust\EntrustFacade::class,
        ];
    }
}
