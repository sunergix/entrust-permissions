<?php

use Mockery as m;
use Zizaco\Entrust\Middleware\EntrustPermission;

class EntrustPermissionTest extends MiddlewareTest
{
    public function testHandle_Guest_ShouldAbort403()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $middleware = new EntrustPermission($guard);

        $guard->shouldReceive('user')->andReturnNull();

        $this->expectAbortCode(403);
        $middleware->handle($request, fn () => null, 'posts.update');
    }

    public function testHandle_IsLoggedInWithNoPermission_ShouldAbort403()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $user = m::mock('_mockedUser');
        $middleware = new EntrustPermission($guard);

        $guard->shouldReceive('user')->andReturn($user);
        $user->shouldReceive('hasPermission')->with(['posts.update', 'posts.delete'])->andReturn(false);

        $this->expectAbortCode(403);
        $middleware->handle($request, fn () => null, 'posts.update|posts.delete');
    }

    public function testHandle_IsLoggedInWithPermission_ShouldNotAbort()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $user = m::mock('_mockedUser');
        $middleware = new EntrustPermission($guard);

        $guard->shouldReceive('user')->andReturn($user);
        $user->shouldReceive('hasPermission')->with(['posts.update'])->andReturn(true);

        $this->assertSame('next', $middleware->handle($request, fn () => 'next', 'posts.update'));
    }
}
