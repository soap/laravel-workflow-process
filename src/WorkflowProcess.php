<?php

namespace Soap\LaravelWorkflowProcess;

use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;

class WorkflowProcess
{
    private string $configFile = 'workflow-process';

    public function __construct() {}

    public function getAuthenticated(): bool
    {
        $guards = config($this->configFile.'.authentication.guards', ['web']);
        $logic = config($this->configFile.'.authentication.logic', 'or');

        if ($logic !== 'and' && $logic !== 'or') {
            throw new InvalidArgumentException('Invalid authentication logic. Use "and" or "or".');
        }

        foreach ($guards as $guard) {
            $isAuthenticated = auth()->guard($guard)->check();

            if ($logic === 'or' && $isAuthenticated) {
                return true;
            }

            if ($logic === 'and' && ! $isAuthenticated) {
                return false;
            }
        }

        return $logic === 'and';
    }

    public function getUser(): ?Authenticatable
    {
        $guards = config($this->configFile.'.authentication.guards', ['web']);

        foreach ($guards as $guard) {
            $user = auth()->guard($guard)->user();
            if ($user !== null) {
                return $user;
            }
        }

        return null;
    }
}
