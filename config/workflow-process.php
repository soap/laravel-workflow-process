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

        // 'isAdmin' => \Soap\LaravelWorkflowProcess\GuardFunctions\AdminGuardFunction::class,
    ],
];
