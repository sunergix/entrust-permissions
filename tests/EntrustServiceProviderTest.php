<?php

namespace Zizaco\Entrust\Tests;

use Illuminate\Support\Facades\Blade;
use Zizaco\Entrust\Entrust;

class EntrustServiceProviderTest extends TestCase
{
    public function testPackageBindsEntrustService(): void
    {
        $this->assertInstanceOf(Entrust::class, $this->app->make(Entrust::class));
        $this->assertSame($this->app->make(Entrust::class), $this->app->make('entrust'));
    }

    public function testPackageMergesDefaultConfig(): void
    {
        $this->assertSame('roles', config('entrust.roles_table'));
        $this->assertSame('permissions', config('entrust.permissions_table'));
        $this->assertSame(3600, config('entrust.cache_ttl'));
    }

    public function testBladeDirectivesAreRegistered(): void
    {
        $this->assertStringContainsString(
            '\\Entrust::hasRole',
            Blade::compileString('@role("admin") visible @endrole')
        );

        $this->assertStringContainsString(
            '\\Entrust::can',
            Blade::compileString('@permission("posts.update") visible @endpermission')
        );

        $this->assertStringContainsString(
            '\\Entrust::ability',
            Blade::compileString('@ability("admin", "posts.update") visible @endability')
        );
    }
}
