<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  list<string>  $permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        $permissions = ! empty($permissions) ? $this->expandPermissions($permissions) : ['access_admin_panel'];

        if ($user->hasAnyPermission(...$permissions)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(Response::HTTP_FORBIDDEN, __('This action is unauthorized.'));
        }

        abort(Response::HTTP_FORBIDDEN, __('You do not have permission to perform this action.'));
    }

    /**
     * Expand delimited permission strings into a unique flat list.
     *
     * @param  list<string>  $permissions
     * @return list<string>
     */
    protected function expandPermissions(array $permissions): array
    {
        return collect($permissions)
            ->flatMap(fn (string $permission) => preg_split('/[|,]/', $permission) ?: [])
            ->map(fn (string $permission) => trim($permission))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
