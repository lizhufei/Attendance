<?php

namespace Hsvisus\Attendance;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class AttendProvider extends ServiceProvider  implements DeferrableProvider
{
    /**
     * 服务提供者加是否延迟加载.
     * @var bool
     */
    protected $defer = true; // 延迟加载服务

    /**
     * Register services.
     * @return void
     */
    public function register()
    {
        $this->app->singleton('attendance', function ($app) {
            return new Attendance();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Configs/attend.php' => config_path('attend.php'),
        ]);
        //数据迁移
        $migrations = [
            __DIR__.'/Migrations/2021_07_27_024952_create_rules_table.php',
            __DIR__.'/Migrations/2021_07_27_065528_create_attendances_table.php',
            __DIR__.'/Migrations/2021_07_27_075728_create_holidays_table.php',
            __DIR__.'/Migrations/2021_08_09_095221_create_machines_table.php',
            __DIR__.'/Migrations/2021_08_09_194201_create_statistics_table.php',
        ];
        $this->loadMigrationsFrom($migrations);
    }
    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return ['attendance'];
    }
}
