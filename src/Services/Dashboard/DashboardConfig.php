<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Dashboard;

/**
 * Dashboard Configuration Manager.
 *
 * Handles dashboard configuration and settings.
 */
class DashboardConfig
{
    /**
     * Check if dashboard is enabled.
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return config('domo.dashboard.enabled', true);
    }

    /**
     * Get dashboard route prefix.
     *
     * @return string
     */
    public static function getRoute(): string
    {
        return config('domo.dashboard.route', 'domo');
    }

    /**
     * Get dashboard middleware.
     *
     * @return array<string>
     */
    public static function getMiddleware(): array
    {
        return config('domo.dashboard.middleware', ['web']);
    }

    /**
     * Get dashboard host.
     *
     * @return string
     */
    public static function getHost(): string
    {
        return env('DOMO_DASHBOARD_HOST', '127.0.0.1');
    }

    /**
     * Get dashboard port.
     *
     * @return int
     */
    public static function getPort(): int
    {
        return (int) env('DOMO_DASHBOARD_PORT', 8080);
    }

    /**
     * Get full dashboard URL.
     *
     * @return string
     */
    public static function getUrl(): string
    {
        return "http://" . self::getHost() . ':' . self::getPort() . '/' . self::getRoute();
    }
}
