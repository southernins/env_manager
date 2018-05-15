<?php
/**
 *
 */

namespace SouthernIns\EnvManager;

use Illuminate\Support\ServiceProvider;
use SouthernIns\EnvManager\Commands\CheckCommand;
use SouthernIns\EnvManager\Commands\PushCommand;
use SouthernIns\EnvManager\Commands\PullCommand;


class EnvManagerServiceProvider extends ServiceProvider {

    public function boot(){
        // Boot runs after ALL providers are registered
    } //- END function boot()

    public function register(){

        if( $this->app->runningInConsole() ){
            $this->commands([
                CheckCommand::class,
                PushCommand::class,
                PullCommand::class,
            ]);
        }

    } //- END function register()

} // - END class BuildServiceProvider{}
