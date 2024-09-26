<?php

namespace Soap\LaravelWorkflowProcess;

use Soap\LaravelWorkflowProcess\Commands\LaravelWorkflowProcessCommand;
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
            ->hasMigration('create_laravel_workflow_process_table')
            ->hasCommand(LaravelWorkflowProcessCommand::class);
    }
}
