<?php

use Illuminate\Http\Request;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class MiddlewareTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }

    public function expectAbortCode(int $code): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(0);
    }

    protected function mockRequest()
    {
        return Request::create('/admin');
    }
}
