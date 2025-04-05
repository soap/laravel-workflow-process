<?php

namespace Soap\LaravelWorkflowProcess;

class WorkflowProcess
{
    private $configFile = 'workflow-process';

    public function __construct() {}

    public function getAuthenticated()
    {
        $guards = config($this->configFile.'.authentication.guards', ['web']);
        $logic = config($this->configFile.'.authentication.logic', 'or');

        if ($logic === 'and') {
            // The user is considered authenticated only if all guards return true.
            $authenticated = true;

            foreach ($guards as $guard) {
                if (! auth()->guard($guard)->check()) {
                    $authenticated = false;
                    break;
                }
            }
        } elseif ($logic === 'or') {
            $authenticated = false;
            foreach ($guards as $guard) {
                if (auth()->guard($guard)->check()) {
                    $authenticated = true;
                    break;
                }
            }
        } else {
            throw new \InvalidArgumentException('Invalid authentication logic. Use "and" or "or".');
        }

        return $authenticated;
    }

    public function getUser()
    {
        $guards = config($this->configFile.'.authentication.guards', ['web']);
        $logic = config($this->configFile.'.authentication.logic', 'or');

        if ($logic === 'and') {
            // The user is considered authenticated only if all guards return true.
            $user = null;

            foreach ($guards as $guard) {
                if (auth()->guard($guard)->check()) {
                    $user = auth()->guard($guard)->user();
                    break;
                }
            }
        } elseif ($logic === 'or') {
            $user = null;
            foreach ($guards as $guard) {
                if (auth()->guard($guard)->check()) {
                    $user = auth()->guard($guard)->user();
                    break;
                }
            }
        } else {
            throw new \InvalidArgumentException('Invalid authentication logic. Use "and" or "or".');
        }

        return $user;
    }
}
