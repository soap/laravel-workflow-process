<?php

use Illuminate\Support\Facades\Auth;
// use Mockery;
use Soap\LaravelWorkflowProcess\GuardFunctions\Authenticated;

afterEach(function () {
    // Ensure that all mock expectations are met and clean up
    Mockery::close();
});

test('compile returns PHP auth check code with default web guard when no argument provided', function () {
    $guardFunction = new Authenticated;
    // No args: uses default '"web"' literal
    $compiled = $guardFunction->compile();
    expect($compiled)->toBe('auth()->guard("web")->check()');
});

test('compile returns PHP auth check code for the given pre-compiled guard literal', function () {
    $guardFunction = new Authenticated;
    // Symfony passes pre-compiled PHP string literals (with surrounding quotes)
    $compiled = $guardFunction->compile('"custom"');
    expect($compiled)->toBe('auth()->guard("custom")->check()');
});

test('evaluate returns true when the default guard is authenticated', function () {
    $guardFunction = new Authenticated;

    // Create a mock for the default "web" guard that returns true for check().
    $guardMock = Mockery::mock();
    $guardMock->shouldReceive('check')->once()->andReturn(true);

    // Expect the default guard to be used.
    Auth::shouldReceive('guard')
        ->once()
        ->with('web')
        ->andReturn($guardMock);

    $result = $guardFunction->evaluate([]);
    expect($result)->toBeTrue();
});

test('evaluate returns false when the custom guard is not authenticated', function () {
    $guardFunction = new Authenticated;

    // Create a mock for the "custom" guard that returns false for check().
    $guardMock = Mockery::mock();
    $guardMock->shouldReceive('check')->once()->andReturn(false);

    // Expect the custom guard to be used.
    Auth::shouldReceive('guard')
        ->once()
        ->with('custom')
        ->andReturn($guardMock);

    $result = $guardFunction->evaluate([], 'custom');
    expect($result)->toBeFalse();
});
