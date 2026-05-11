<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Zizaco\Entrust\Entrust;

class EntrustTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    public function testHasRole()
    {
        $app = new stdClass();
        $entrust = m::mock('Zizaco\Entrust\Entrust[user]', [$app]);
        $user = m::mock('_mockedUser');

        $entrust->shouldReceive('user')
            ->andReturn($user)
            ->twice()->ordered();

        $entrust->shouldReceive('user')
            ->andReturn(false)
            ->once()->ordered();

        $user->shouldReceive('hasRole')
            ->with('UserRole', false)
            ->andReturn(true)
            ->once();

        $user->shouldReceive('hasRole')
            ->with('NonUserRole', false)
            ->andReturn(false)
            ->once();

        $this->assertTrue($entrust->hasRole('UserRole'));
        $this->assertFalse($entrust->hasRole('NonUserRole'));
        $this->assertFalse($entrust->hasRole('AnyRole'));
    }

    public function testHasPermission()
    {
        $app = new stdClass();
        $entrust = m::mock('Zizaco\Entrust\Entrust[user]', [$app]);
        $user = m::mock('_mockedUser');

        $entrust->shouldReceive('user')
            ->andReturn($user)
            ->twice()->ordered();

        $entrust->shouldReceive('user')
            ->andReturn(false)
            ->once()->ordered();

        $user->shouldReceive('hasPermission')
            ->with('user_can', false)
            ->andReturn(true)
            ->once();

        $user->shouldReceive('hasPermission')
            ->with('user_cannot', false)
            ->andReturn(false)
            ->once();

        $this->assertTrue($entrust->hasPermission('user_can'));
        $this->assertFalse($entrust->hasPermission('user_cannot'));
        $this->assertFalse($entrust->hasPermission('any_permission'));
    }

    public function testCanDelegatesToHasPermission()
    {
        $app = new stdClass();
        $entrust = m::mock('Zizaco\Entrust\Entrust[hasPermission]', [$app]);

        $entrust->shouldReceive('hasPermission')
            ->with('user_can', false)
            ->andReturn(true)
            ->once();

        $this->assertTrue($entrust->can('user_can'));
    }

    public function testUser()
    {
        $app = new stdClass();
        $app->auth = m::mock();
        $entrust = new Entrust($app);
        $user = m::mock('_mockedUser');

        $app->auth->shouldReceive('user')
            ->andReturn($user)
            ->once();

        $this->assertSame($user, $entrust->user());
    }
}
