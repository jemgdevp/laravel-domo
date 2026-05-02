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
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the current URL.
     *
     * @return string
     */
    public function getCurrentUrl(): string
    {
        return $this->request->fullUrl();
    }

    /**
     * Get the base URL for the dashboard.
     *
     * @return string
     */
    public function getDashboardBaseUrl(): string
    {
        return config('domo.dashboard.route', 'domo');
    }

    /**
     * Check if the current request is for the dashboard.
     *
     * @return bool
     */
    public function isDashboardRequest(): bool
    {
        return $this->request->is($this->getDashboardBaseUrl() . '/*');
    }

    /**
     * Resolve a dashboard route.
     *
     * @param string $route
     * @return string
     */
    public function resolveRoute(string $route): string
    {
        return rtrim($this->getDashboardBaseUrl(), '/') . '/' . ltrim($route, '/');
    }
}
