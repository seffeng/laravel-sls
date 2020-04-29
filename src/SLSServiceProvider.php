<?php

namespace Seffeng\LaravelSLS;

use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class SLSServiceProvider extends BaseServiceProvider
{
    /**
     *
     * {@inheritDoc}
     * @see \Illuminate\Support\ServiceProvider::register()
     */
    public function register()
    {
        $this->registerAliases();
        $this->mergeConfigFrom($this->configPath(), 'sls');

        $this->app->singleton('sls', function ($app) {
            $config = $app['config']->get('sls');
            if ($config && is_array($config)) {
                return new SLSLog($config);
            } else {
                throw new \RuntimeException('Please execute the command `php artisan vendor:publish --tag="sls"` first to  generate sms configuration file.');
            }
        });

        $config = $this->app['config']['sls'];
        $this->app->instance('sls.writer', new Writer(app('sls'), $this->app['events'], $config['topic'], $config['env']));
    }

    /**
     *
     * @author zxf
     * @date    2020年4月17日
     */
    public function boot()
    {
        if ($this->app->runningInConsole() && $this->app instanceof LaravelApplication) {
            $this->publishes([$this->configPath() => config_path('sls.php')], 'sls');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('sls');
        }
    }

    /**
     *
     * @author zxf
     * @date    2020年4月18日
     */
    protected function registerAliases()
    {
        $this->app->alias('sls', SLSLog::class);
        $this->app->alias('sls.writer', Writer::class);
    }

    /**
     *
     * @author zxf
     * @date    2020年4月17日
     * @return string
     */
    protected function configPath()
    {
        return dirname(__DIR__) . '/config/sls.php';
    }
}
