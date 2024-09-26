<?php

namespace Soap\LaravelWorkflowProcess;

use Illuminate\Support\Facades\Event;
use Soap\LaravelWorkflowProcess\Commands\LaravelWorkflowProcessCommand;
use Soap\LaravelWorkflowProcess\Listeners\WorkflowGuardSubscriber;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWorkflowProcessServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-workflow-process')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(LaravelWorkflowProcessCommand::class);
    }

    public function packageBooted()
    {
        Event::subscribe(WorkflowGuardSubscriber::class);
    }
}
