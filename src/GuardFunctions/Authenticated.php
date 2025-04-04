<?php

namespace Soap\LaravelWorkflowProcess\GuardFunctions;

use Soap\LaravelWorkflowProcess\Contracts\GuardFunctionInterface;

class Authenticated implements GuardFunctionInterface
{
    public function compile(...$args): string
    {
        // Compiler returns a placeholder.
        $guard = $args[0] ?? 'web';

        return sprintf('authenticated("%s")', $guard);
    }

    public function evaluate(array $variables, ...$args)
    {
        // Get the guard name, defaulting to 'web'
        $guard = $args[0] ?? 'web';

        return auth()->guard($guard)->check();
    }
}
