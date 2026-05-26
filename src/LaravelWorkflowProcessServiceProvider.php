<?php

namespace Soap\LaravelWorkflowProcess;

use Illuminate\Support\Facades\Event;
use Soap\LaravelWorkflowProcess\Commands\LaravelWorkflowProcessCommand;
use Soap\LaravelWorkflowProcess\Listeners\WorkflowGuardSubscriber;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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

    public function registeringPackage()
    {
        $this->app->singleton(GuardEvaluator::class, function ($app) {
            return new GuardEvaluator(new ExpressionLanguage);
        });

        $this->app->singleton(WorkflowProcess::class, function ($app) {
            return new WorkflowProcess;
        });

        $this->app->alias(WorkflowProcess::class, 'workflow-process');
    }

    public function packageBooted()
    {
        Event::subscribe(WorkflowGuardSubscriber::class);
    }
}
