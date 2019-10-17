<?php

namespace Ryu\Seat\Tax;

use Herpaderpaldent\Seat\SeatGroups\Events\GroupApplication;
use Herpaderpaldent\Seat\SeatGroups\Events\GroupSynced;
use Herpaderpaldent\Seat\SeatGroups\Events\GroupSyncFailed;
use Herpaderpaldent\Seat\SeatGroups\Events\MissingRefreshToken;
use Herpaderpaldent\Seat\SeatGroups\Listeners\CreateSyncedSeatLogsEntry;
use Herpaderpaldent\Seat\SeatGroups\Listeners\CreateSyncFailedLogsEntry;
use Herpaderpaldent\Seat\SeatGroups\Listeners\GroupApplicationNotification;
use Herpaderpaldent\Seat\SeatGroups\Listeners\GroupSyncedNotification;
use Herpaderpaldent\Seat\SeatGroups\Listeners\GroupSyncFailedNotification;
use Herpaderpaldent\Seat\SeatGroups\Listeners\MissingRefreshTokenLogsEntry;
use Herpaderpaldent\Seat\SeatGroups\Listeners\MissingRefreshTokenNotification;
use Herpaderpaldent\Seat\SeatGroups\Observers\RefreshTokenObserver;

use Illuminate\Support\Arr;

use Seat\Eveapi\Models\RefreshToken;
use Seat\Services\AbstractSeatPlugin;

class TaxServiceProvider extends AbstractSeatPlugin
{

    /**
     * Bootstrap the application services.
     * 引导应用程序服务。
     *
     * @return void
     */
    public function boot()
    {

        //
        $this->addCommands();
        // 路由
        $this->addRoutes();
        // 视图
        $this->addViews();

        // 多语言
        $this->addTranslations();

        // 加载数据库
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');

        RefreshToken::observe(RefreshTokenObserver::class);

        $this->add_events();
    }

    /**
     * Register the application services.
     * 注册应用程序服务。
     *
     * @return void
     */
    public function register()
    {
        // 允许
        $this->mergeConfigFrom(__DIR__ . '/config/tax.permission.php', 'seat_tax.permissions');

        // 配置
        $this->mergeConfigFrom(__DIR__ . '/config/tax.config.php', 'seat_tax.config');

        // 侧边栏
        $this->mergeConfigFrom(__DIR__ . '/config/tax.sidebar.php', 'seat_tax.sidebar');

        // 服务
        $this->mergeConfigFrom(__DIR__ . '/config/tax.services.php', 'seat_tax.services');
    }

    private function addCommands()
    {
        $this->commands([
            Commands\SeatTaxUpdate::class,
        ]);
    }

    /**
     * 加入路由
     */
    private function addRoutes()
    {
        if (! $this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    private function addViews()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'seat_tax');
    }

    /**
     * 加入语言包
     */
    private function addTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'seat_tax');
    }

    /**
     * 加入事件
     */
    private function add_events()
    {
        $this->app->events->listen(GroupSynced::class, CreateSyncedSeatLogsEntry::class);
//        $this->app->events->listen(GroupSynced::class, GroupSyncedNotification::class);
//
//        $this->app->events->listen(GroupSyncFailed::class, CreateSyncFailedLogsEntry::class);
//        $this->app->events->listen(GroupSyncFailed::class, GroupSyncFailedNotification::class);
//
//        $this->app->events->listen(MissingRefreshToken::class, MissingRefreshTokenLogsEntry::class);
//        $this->app->events->listen(MissingRefreshToken::class, MissingRefreshTokenNotification::class);
//
//        $this->app->events->listen(GroupApplication::class, GroupApplicationNotification::class);
    }

    /**
     * Merge the given configuration with the existing configuration.
     * https://medium.com/@koenhoeijmakers/properly-merging-configs-in-laravel-packages-a4209701746d.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);
        $this->app['config']->set($key, $this->mergeConfigs(require $path, $config));
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     * https://medium.com/@koenhoeijmakers/properly-merging-configs-in-laravel-packages-a4209701746d.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeConfigs(array $original, array $merging)
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfigs($value, $merging[$key]);
        }

        return $array;
    }

    /**
     * Return an URI to a CHANGELOG.md file or an API path which will be providing changelog history.
     *
     * @return string|null
     */
    public function getChangelogUri(): ?string
    {

        return 'https://raw.githubusercontent.com/herpaderpaldent/seat-groups/master/CHANGELOG.md';
    }

    /**
     * Return the plugin public name as it should be displayed into settings.
     *
     * @return string
     */
    public function getName(): string
    {

        return 'seat-tax';
    }

    /**
     * Return the plugin repository address.
     *
     * @return string
     */
    public function getPackageRepositoryUrl(): string
    {

        return 'https://github.com/herpaderpaldent/seat-groups';
    }

    /**
     * Return the plugin technical name as published on package manager.
     *
     * @return string
     */
    public function getPackagistPackageName(): string
    {

        return 'seat-groups';
    }

    /**
     * Return the plugin vendor tag as published on package manager.
     *
     * @return string
     */
    public function getPackagistVendorName(): string
    {

        return 'herpaderpaldent';
    }

    /**
     * Return the plugin installed version.
     *
     * @return string
     */
    public function getVersion(): string
    {

        return config('tax.config.version');
    }
}
