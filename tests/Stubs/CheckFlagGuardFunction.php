<?php

namespace Soap\LaravelWorkflowProcess\Tests\Stubs;

use Soap\LaravelWorkflowProcess\Contracts\GuardFunctionInterface;

class CheckFlagGuardFunction implements GuardFunctionInterface
{
    public function compile(...$args): string
    {
        // The compiler just returns a placeholder.
        return 'true';
    }

    public function evaluate(array $variables, ...$args): mixed
    {
        return $variables['flag'] ?? false;
    }
}
