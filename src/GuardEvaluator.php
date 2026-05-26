<?php

namespace Soap\LaravelWorkflowProcess;

use InvalidArgumentException;
use Soap\LaravelWorkflowProcess\Contracts\GuardFunctionInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class GuardEvaluator
{
    protected ExpressionLanguage $expressionLanguage;

    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->registerCustomFunctions();
    }

    protected function registerCustomFunctions(): void
    {
        $customFunctions = config('workflow-process.custom_functions', []);

        foreach ($customFunctions as $name => $definition) {
            if (is_string($definition) && class_exists($definition)) {
                $instance = app($definition);
                if ($instance instanceof GuardFunctionInterface) {
                    $compiler = [$instance, 'compile'];
                    $evaluator = [$instance, 'evaluate'];
                } else {
                    throw new InvalidArgumentException("Class {$definition} must implement GuardFunctionInterface.");
                }
            } elseif (is_array($definition)) {
                $compiler = $definition['compiler'] ?? function () {
                    return 'true';
                };
                $evaluator = $definition['evaluator'] ?? function (array $_variables) {
                    return true;
                };
            } else {
                continue;
            }

            $this->expressionLanguage->register($name, $compiler, $evaluator);
        }
    }

    public function evaluate(string $expression, array $variables = []): mixed
    {
        return $this->expressionLanguage->evaluate($expression, $variables);
    }
}
