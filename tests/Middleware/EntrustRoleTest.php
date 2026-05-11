<?php

use Mockery as m;
use Zizaco\Entrust\Middleware\EntrustRole;

class EntrustRoleTest extends MiddlewareTest
{
    public function testHandle_Guest_ShouldAbort403()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $middleware = new EntrustRole($guard);

        $guard->shouldReceive('user')->andReturnNull();

        $this->expectAbortCode(403);
        $middleware->handle($request, fn () => null, 'admin');
    }

    public function testHandle_IsLoggedInWithMismatchRole_ShouldAbort403()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $user = m::mock('_mockedUser');
        $middleware = new EntrustRole($guard);

        $guard->shouldReceive('user')->andReturn($user);
        $user->shouldReceive('hasRole')->with(['admin', 'editor'])->andReturn(false);

        $this->expectAbortCode(403);
        $middleware->handle($request, fn () => null, 'admin|editor');
    }

    public function testHandle_IsLoggedInWithMatchingRole_ShouldNotAbort()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $user = m::mock('_mockedUser');
        $middleware = new EntrustRole($guard);

        $guard->shouldReceive('user')->andReturn($user);
        $user->shouldReceive('hasRole')->with(['admin'])->andReturn(true);

        $this->assertSame('next', $middleware->handle($request, fn () => 'next', 'admin'));
    }
}
