<?php

namespace Soap\LaravelWorkflowProcess\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Soap\LaravelWorkflowProcess\LaravelWorkflowProcess
 */
class LaravelWorkflowProcess extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Soap\LaravelWorkflowProcess\LaravelWorkflowProcess::class;
    }
}
