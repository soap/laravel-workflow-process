<?php

// config for Soap/LaravelWorkflowProcess
return [
    'custom_functions' => [
        // 'function_name' => [
        //     'compiler'  => function () { return 'true'; },
        //     'evaluator' => function (array $variables) { return true; },
        // ],
        // 'function_name' => 'App\CustomGuardFunction',
        // 'authenicated' => \Soap\LaravelWorkflowProcess\GuardFunctions\Authenticated::class,

        'authenticated' => [
            'compiler' => function ($guard = 'web') {
                return sprintf('authenticated("%s")', $guard);
            },
            'evaluator' => function (array $variables, $guard = 'web') {
                // This allows checking a specific guard (e.g., 'web', 'api', etc.)
                return auth()->guard($guard)->check();
            },
        ],
    ],
];
