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

class EntrustAbility
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
	 * @param \Illuminate\Http\Request $request
	 * @param Closure $next
	 * @param $roles
	 * @param $permissions
	 * @param bool $validateAll
	 * @return mixed
	 */
    public function handle(
        Request $request,
        Closure $next,
        string|array|null $roles,
        string|array|null $permissions,
        bool|string $validateAll = false
    ): mixed {
        $user = $this->auth->user();
        $roles = $this->parse($roles);
        $permissions = $this->parse($permissions);
        $validateAll = filter_var($validateAll, FILTER_VALIDATE_BOOLEAN);

        if (! $user || ! $user->ability($roles, $permissions, ['validate_all' => $validateAll])) {
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
