<?php

// config for Soap/LaravelWorkflowProcess
return [
    'authentication' => [
        'guards' => ['web'],
        'logic' => 'or', // 'and' or 'or'
    ],
    'custom_functions' => [
        // 'function_name' => [
        //     'compiler'  => function () { return 'true'; },
        //     'evaluator' => function (array $variables) { return true; },
        // ],

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
