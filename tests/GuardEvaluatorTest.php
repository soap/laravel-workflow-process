<?php

use Soap\LaravelWorkflowProcess\GuardEvaluator;
use Soap\LaravelWorkflowProcess\Tests\Stubs\CheckFlagGuardFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

it('evaluates a custom closure guard function correctly', function () {
    // Set a test configuration for custom guard functions.
    // Here we define a function named "checkFlag" that returns the value of a 'flag' variable.
    config()->set('workflow-process.custom_functions', [
        'checkFlag' => [
            'compiler' => function () {
                // The compiler returns a placeholder expression.
                return 'true';
            },
            'evaluator' => function (array $variables) {
                // Evaluate by returning the 'flag' variable or false if not set.
                return $variables['flag'] ?? false;
            },
        ],
    ]);

    // Instantiate the ExpressionLanguage and GuardEvaluator.
    $expressionLanguage = new ExpressionLanguage;
    $guardEvaluator = new GuardEvaluator($expressionLanguage);

    // Evaluate the expression with 'flag' set to true.
    $resultTrue = $guardEvaluator->evaluate('checkFlag()', ['flag' => true]);
    expect($resultTrue)->toBeTrue();

    // Evaluate the expression with 'flag' set to false.
    $resultFalse = $guardEvaluator->evaluate('checkFlag()', ['flag' => false]);
    expect($resultFalse)->toBeFalse();
});

it('throws InvalidArgumentException when class does not implement GuardFunctionInterface', function () {
    config()->set('workflow-process.custom_functions', [
        'badFunction' => stdClass::class,
    ]);

    $expressionLanguage = new ExpressionLanguage;

    expect(fn () => new GuardEvaluator($expressionLanguage))
        ->toThrow(InvalidArgumentException::class, 'must implement GuardFunctionInterface');
});

it('silently skips definitions that are neither a string nor an array', function () {
    config()->set('workflow-process.custom_functions', [
        'skipped' => 42, // invalid — neither string nor array
    ]);

    $expressionLanguage = new ExpressionLanguage;

    // Should not throw; the invalid entry is silently ignored
    $guardEvaluator = new GuardEvaluator($expressionLanguage);

    // Basic evaluation still works after skipping
    expect($guardEvaluator->evaluate('1 + 1'))->toBe(2);
});

it('evaluates a custom guard function defined as a class correctly', function () {
    // Set the configuration so that the custom function points to our evaluator class.
    config()->set('workflow-process.custom_functions', [
        'checkFlag' => CheckFlagGuardFunction::class,
    ]);

    // Instantiate the ExpressionLanguage and GuardEvaluator.
    $expressionLanguage = new ExpressionLanguage;
    $guardEvaluator = new GuardEvaluator($expressionLanguage);

    // Evaluate the expression with 'flag' set to true.
    $resultTrue = $guardEvaluator->evaluate('checkFlag()', ['flag' => true]);
    expect($resultTrue)->toBeTrue();

    // Evaluate the expression with 'flag' set to false.
    $resultFalse = $guardEvaluator->evaluate('checkFlag()', ['flag' => false]);
    expect($resultFalse)->toBeFalse();
});
