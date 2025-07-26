<?php

namespace App\Http\Middleware;

use App\Repositories\Auth\AuthRepository;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyAdminCanDoThisAction
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = app(AuthRepository::class)->isAdmin();
        if (! $isAdmin) {
            return $this->sendError(__('messages.auth.only_admin_can_do_this_action'), 403);
        }

        return $next($request);
    }
}
