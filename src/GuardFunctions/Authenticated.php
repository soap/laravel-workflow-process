<?php

namespace Soap\LaravelWorkflowProcess\GuardFunctions;

use Soap\LaravelWorkflowProcess\Contracts\GuardFunctionInterface;

class Authenticated implements GuardFunctionInterface
{
    public function compile(...$args): string
    {
        // $args[0] is the pre-compiled guard string passed by Symfony ExpressionLanguage
        // e.g. for authenticated("web"), Symfony passes '"web"' (a PHP string literal)
        $compiledGuard = $args[0] ?? '"web"';

        return sprintf('auth()->guard(%s)->check()', $compiledGuard);
    }

    public function evaluate(array $_variables, ...$args): bool
    {
        $guard = $args[0] ?? 'web';

        return auth()->guard($guard)->check();
    }
}
