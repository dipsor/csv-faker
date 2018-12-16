<?php
namespace Dipsor\CsvFaker;

use Dipsor\CsvFaker\Lib\CsvBuilder;
use Faker\Factory;
use Faker\Generator as Faker;
use Dipsor\CsvFaker\Console\Commands\FakeCsvCommand;
use Illuminate\Support\ServiceProvider;

class CsvFakerServiceProvider  extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
//        $this->publishes([
//            __DIR__ . '/config/config.php' => config_path('laravel-ab.php'),
//        ], 'config');
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
//        $this->mergeConfigFrom(
//            __DIR__.'/config/config.php', 'laravel-ab'
//        );
//        $this->app->make('Illuminate\Contracts\Http\Kernel')->prependMiddleware('\Digitonic\LaravelAb\App\Http\Middleware\LaravelAbMiddleware');
//        $this->app->bind('Ab', 'Digitonic\LaravelAb\App\Ab');
//        $this->registerCompiler();
        $this->registerCommands();
    }
    public function registerCommands(){
        $this->app->singleton('csv:faker:new', function($app) {
            return new FakeCsvCommand(new CsvBuilder(Factory::create()));
        });
        $this->commands('csv:faker:new');
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'csv:faker:new'
        ];
    }
}