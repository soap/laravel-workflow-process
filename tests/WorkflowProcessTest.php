<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Soap\LaravelWorkflowProcess\WorkflowProcess;

afterEach(function () {
    Mockery::close();
});

// ---------------------------------------------------------------------------
// getAuthenticated() — OR logic
// ---------------------------------------------------------------------------

test('getAuthenticated returns true when first guard is authenticated (or logic)', function () {
    config()->set('workflow-process.authentication.guards', ['web']);
    config()->set('workflow-process.authentication.logic', 'or');

    $guardMock = Mockery::mock();
    $guardMock->shouldReceive('check')->once()->andReturn(true);
    Auth::shouldReceive('guard')->with('web')->andReturn($guardMock);

    expect((new WorkflowProcess)->getAuthenticated())->toBeTrue();
});

test('getAuthenticated returns false when no guard is authenticated (or logic)', function () {
    config()->set('workflow-process.authentication.guards', ['web', 'api']);
    config()->set('workflow-process.authentication.logic', 'or');

    $webMock = Mockery::mock();
    $webMock->shouldReceive('check')->once()->andReturn(false);

    $apiMock = Mockery::mock();
    $apiMock->shouldReceive('check')->once()->andReturn(false);

    Auth::shouldReceive('guard')->with('web')->andReturn($webMock);
    Auth::shouldReceive('guard')->with('api')->andReturn($apiMock);

    expect((new WorkflowProcess)->getAuthenticated())->toBeFalse();
});

test('getAuthenticated short-circuits on first authenticated guard (or logic)', function () {
    config()->set('workflow-process.authentication.guards', ['web', 'api']);
    config()->set('workflow-process.authentication.logic', 'or');

    $webMock = Mockery::mock();
    $webMock->shouldReceive('check')->once()->andReturn(true);

    // 'api' guard must NOT be checked — once 'web' passes, loop stops
    Auth::shouldReceive('guard')->with('web')->andReturn($webMock);
    Auth::shouldReceive('guard')->with('api')->never();

    expect((new WorkflowProcess)->getAuthenticated())->toBeTrue();
});

// ---------------------------------------------------------------------------
// getAuthenticated() — AND logic
// ---------------------------------------------------------------------------

test('getAuthenticated returns true when all guards are authenticated (and logic)', function () {
    config()->set('workflow-process.authentication.guards', ['web', 'api']);
    config()->set('workflow-process.authentication.logic', 'and');

    $webMock = Mockery::mock();
    $webMock->shouldReceive('check')->once()->andReturn(true);

    $apiMock = Mockery::mock();
    $apiMock->shouldReceive('check')->once()->andReturn(true);

    Auth::shouldReceive('guard')->with('web')->andReturn($webMock);
    Auth::shouldReceive('guard')->with('api')->andReturn($apiMock);

    expect((new WorkflowProcess)->getAuthenticated())->toBeTrue();
});

test('getAuthenticated returns false when one guard fails (and logic)', function () {
    config()->set('workflow-process.authentication.guards', ['web', 'api']);
    config()->set('workflow-process.authentication.logic', 'and');

    $webMock = Mockery::mock();
    $webMock->shouldReceive('check')->once()->andReturn(false);

    // 'api' must NOT be checked — loop exits early on first failure
    Auth::shouldReceive('guard')->with('web')->andReturn($webMock);
    Auth::shouldReceive('guard')->with('api')->never();

    expect((new WorkflowProcess)->getAuthenticated())->toBeFalse();
});

// ---------------------------------------------------------------------------
// getAuthenticated() — invalid logic
// ---------------------------------------------------------------------------

test('getAuthenticated throws InvalidArgumentException for unknown logic value', function () {
    config()->set('workflow-process.authentication.guards', ['web']);
    config()->set('workflow-process.authentication.logic', 'xor');

    expect(fn () => (new WorkflowProcess)->getAuthenticated())
        ->toThrow(InvalidArgumentException::class, 'Invalid authentication logic. Use "and" or "or".');
});

// ---------------------------------------------------------------------------
// getUser()
// ---------------------------------------------------------------------------

test('getUser returns the authenticated user from the first matching guard', function () {
    config()->set('workflow-process.authentication.guards', ['web']);

    $user = Mockery::mock(Authenticatable::class);

    $guardMock = Mockery::mock();
    $guardMock->shouldReceive('user')->once()->andReturn($user);
    Auth::shouldReceive('guard')->with('web')->andReturn($guardMock);

    expect((new WorkflowProcess)->getUser())->toBe($user);
});

test('getUser returns null when no guard has an authenticated user', function () {
    config()->set('workflow-process.authentication.guards', ['web', 'api']);

    $webMock = Mockery::mock();
    $webMock->shouldReceive('user')->once()->andReturn(null);

    $apiMock = Mockery::mock();
    $apiMock->shouldReceive('user')->once()->andReturn(null);

    Auth::shouldReceive('guard')->with('web')->andReturn($webMock);
    Auth::shouldReceive('guard')->with('api')->andReturn($apiMock);

    expect((new WorkflowProcess)->getUser())->toBeNull();
});

test('getUser skips unauthenticated guards and returns user from the first authenticated one', function () {
    config()->set('workflow-process.authentication.guards', ['web', 'api']);

    $user = Mockery::mock(Authenticatable::class);

    $webMock = Mockery::mock();
    $webMock->shouldReceive('user')->once()->andReturn(null);

    $apiMock = Mockery::mock();
    $apiMock->shouldReceive('user')->once()->andReturn($user);

    Auth::shouldReceive('guard')->with('web')->andReturn($webMock);
    Auth::shouldReceive('guard')->with('api')->andReturn($apiMock);

    expect((new WorkflowProcess)->getUser())->toBe($user);
});
