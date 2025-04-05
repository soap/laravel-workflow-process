<?php

namespace Soap\LaravelWorkflowProcess\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Soap\LaravelWorkflowProcess\WorkflowProcess
 */
class LaravelWorkflowProcess extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Soap\LaravelWorkflowProcess\WorkflowProcess::class;
    }
}
