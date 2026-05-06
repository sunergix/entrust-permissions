<?php

use Mockery as m;
use Zizaco\Entrust\Middleware\EntrustAbility;

class EntrustAbilityTest extends MiddlewareTest
{
    public function testHandle_Guest_ShouldAbort403()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $middleware = new EntrustAbility($guard);

        $guard->shouldReceive('user')->andReturnNull();

        $this->expectAbortCode(403);
        $middleware->handle($request, fn () => null, 'admin', 'posts.update');
    }

    public function testHandle_IsLoggedInWithNoAbility_ShouldAbort403()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $user = m::mock('_mockedUser');
        $middleware = new EntrustAbility($guard);

        $guard->shouldReceive('user')->andReturn($user);
        $user->shouldReceive('ability')
            ->with(['admin'], ['posts.update'], ['validate_all' => true])
            ->andReturn(false);

        $this->expectAbortCode(403);
        $middleware->handle($request, fn () => null, 'admin', 'posts.update', 'true');
    }

    public function testHandle_IsLoggedInWithAbility_ShouldNotAbort()
    {
        $guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $request = $this->mockRequest();
        $user = m::mock('_mockedUser');
        $middleware = new EntrustAbility($guard);

        $guard->shouldReceive('user')->andReturn($user);
        $user->shouldReceive('ability')
            ->with(['admin'], ['posts.update'], ['validate_all' => false])
            ->andReturn(true);

        $this->assertSame('next', $middleware->handle($request, fn () => 'next', 'admin', 'posts.update'));
    }
}
