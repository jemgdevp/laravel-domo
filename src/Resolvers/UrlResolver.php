<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Resolvers;

use Illuminate\Http\Request;

/**
 * URL resolver for dashboard routes.
 *
 * Resolves and validates URLs for the web dashboard.
 */
class UrlResolver
{
    /**
     * The current request instance.
     */
    protected Request $request;

    /**
     * Create a new URL resolver instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the current URL.
     */
    public function getCurrentUrl(): string
    {
        return $this->request->fullUrl();
    }

    /**
     * Get the base URL for the dashboard.
     */
    public function getDashboardBaseUrl(): string
    {
        return config('domo.dashboard.route', 'domo');
    }

    /**
     * Check if the current request is for the dashboard.
     */
    public function isDashboardRequest(): bool
    {
        $base = $this->getDashboardBaseUrl();

        return $this->request->is($base) || $this->request->is("{$base}/*");
    }

    /**
     * Resolve a dashboard route.
     */
    public function resolveRoute(string $route): string
    {
        return rtrim($this->getDashboardBaseUrl(), '/').'/'.ltrim($route, '/');
    }
}
