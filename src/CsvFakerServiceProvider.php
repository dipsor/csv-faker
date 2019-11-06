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
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
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

    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        // Load the helpers in app/Http/helpers.php
        if (file_exists($file = app_path('Http/helpers.php')))
        {
            require $file;
        }
    }
}
