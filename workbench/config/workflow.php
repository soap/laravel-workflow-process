<?php

return [
    'post' => [
        'type' => 'workflow',
        'supports' => ['Workbench\App\Models\Post'],
        'marking_store' => [
            'property' => 'status',
        ],
        'places' => [
            'draft',
            'review',
            'published',
            'archived',
        ],
        'transitions' => [
            'submit' => [
                'from' => ['draft'],
                'to' => 'review',
                'metadata' => [
                    'guard' => 'authenticated && subject.user_id == user.id',
                ],
            ],
            'approve' => [
                'from' => ['review'],
                'to' => 'published',
            ],
            'reject' => [
                'from' => ['review'],
                'to' => 'draft',
            ],
            'archive' => [
                'from' => ['published'],
                'to' => 'archived',
            ],
        ],
    ],
];
