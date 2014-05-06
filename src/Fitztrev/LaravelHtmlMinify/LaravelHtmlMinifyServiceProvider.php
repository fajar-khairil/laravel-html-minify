<?php 
namespace Fitztrev\LaravelHtmlMinify;

use Illuminate\View\ViewServiceProvider as ViewServiceProvider;
use Illuminate\View\Engines\CompilerEngine;
class LaravelHtmlMinifyServiceProvider extends ViewServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('fitztrev/laravel-html-minify');
    }

    /**
     * Register the Blade engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerBladeEngine($resolver)
    {
        $app = $this->app;

        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Blade compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $app->bindShared('blade.compiler', function($app)
        {
            return new LaravelHtmlMinifyCompiler(
                $app['config']->get('laravel-html-minify::views'),
                $app['files'], 
                $app['path.storage'].'/views'
            );
        });

        $resolver->register('blade', function() use ($app)
        {
            return new CompilerEngine($app['blade.compiler'], $app['files']);
        });
    }
}
