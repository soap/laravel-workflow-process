<?php

namespace Soap\LaravelWorkflowProcess;

use Soap\LaravelWorkflowProcess\Contracts\GuardFunctionInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class GuardEvaluator
{
    protected $expressionLanguage;

    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->registerCustomFunctions();
    }

    protected function registerCustomFunctions()
    {
        $customFunctions = config('workflow-process.custom_functions', []);

        foreach ($customFunctions as $name => $definition) {
            if (is_string($definition) && class_exists($definition)) {
                // Instantiate the class via Laravel's container.
                $instance = app($definition);
                if ($instance instanceof GuardFunctionInterface) {
                    $compiler = [$instance, 'compile'];
                    $evaluator = [$instance, 'evaluate'];
                } else {
                    throw new \Exception("Class {$definition} must implement GuardFunctionInterface.");
                }
            } elseif (is_array($definition)) {
                $compiler = $definition['compiler'] ?? function () {
                    return 'true';
                };
                $evaluator = $definition['evaluator'] ?? function (array $variables) {
                    return true;
                };
            } else {
                continue; // Skip invalid definitions.
            }

            $this->expressionLanguage->register($name, $compiler, $evaluator);
        }
    }

    public function evaluate(string $expression, array $variables = [])
    {
        return $this->expressionLanguage->evaluate($expression, $variables);
    }
}
