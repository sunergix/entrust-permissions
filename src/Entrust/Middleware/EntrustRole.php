<?php

namespace Zizaco\Entrust\Middleware;

/**
 * This file is part of Entrust,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Zizaco\Entrust
 */

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class EntrustRole
{
    private const DELIMITER = '|';

    protected Guard $auth;

	/**
	 * Creates a new instance of the middleware.
	 *
	 * @param Guard $auth
	 */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  Closure $next
	 * @param  $roles
	 * @return mixed
	 */
    public function handle(Request $request, Closure $next, string|array|null $roles): mixed
    {
        $user = $this->auth->user();
        $roles = $this->parse($roles);

        if (! $user || ! $user->hasRole($roles)) {
            abort(403);
        }

        return $next($request);
    }

    protected function parse(string|array|null $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return array_values(array_filter(explode(self::DELIMITER, $value ?? ''), 'strlen'));
    }
}
