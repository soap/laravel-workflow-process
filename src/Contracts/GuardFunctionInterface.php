<?php

namespace Soap\LaravelWorkflowProcess\Contracts;

interface GuardFunctionInterface
{
    /**
     * Compile the function. Used by the ExpressionLanguage compiler.
     * This is the function that will be called when the expression is compiled.
     * It is used for caching the compiled expression.
     *
     * @param  mixed  ...$args
     */
    public function compile(...$args): string;

    /**
     * Evaluate the function. Executed during the evaluation.
     *
     * @param  mixed  ...$args
     * @return mixed
     */
    public function evaluate(array $variables, ...$args);
}
