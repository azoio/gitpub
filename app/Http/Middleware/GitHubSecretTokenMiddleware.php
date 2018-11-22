<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GitHubSecretTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app()->configure('githubrepo');

        $sig_check = 'sha1=' . hash_hmac('sha1', $request->getContent(), config('githubrepo.webhook-secret-token'));

        if ($sig_check !== $request->header('x-hub-signature')) {
            return response(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}