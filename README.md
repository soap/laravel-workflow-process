# Provides Laravel workflow guards using Symfony expression and related objects

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soap/laravel-workflow-process.svg?style=flat-square)](https://packagist.org/packages/soap/laravel-workflow-process)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/soap/laravel-workflow-process/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/soap/laravel-workflow-process/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/soap/laravel-workflow-process/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/soap/laravel-workflow-process/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/soap/laravel-workflow-process.svg?style=flat-square)](https://packagist.org/packages/soap/laravel-workflow-process)

Using [Zerodahero's Laravel Workflow (based on Symfony Workflow)](https://github.com/zerodahero/laravel-workflow) to handle state-transition workflow is great. However, coding transition guard in event is hard. This package provides a simple way, you can add Symfony Expression Language as a transition guard for each transition. This configuration must provided in transiton metadata using 'guard' as a key.
This package subscribes for all workflows' transition guard events and uses provided Symfony Expression Language to allow to block the transition.

## Support us


## Installation

You can install the package via composer:

```bash
composer require soap/laravel-workflow-process
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-workflow-process-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage
Task that you have to do is providing guard configuration like the following example. This is in laravel-workflow 's configuration file (config/workflow.php). If you want to store workflow configuration in database, please visis [my Laravel Workflow Loader package](https://github.com/soap/laravel-workflow-loader).

```php
// file config/workflow.php
use ZeroDaHero\LaravelWorkflow\MarkingStores\EloquentMarkingStore;

return [
    'blogPost' => [
        'type' => 'workflow',
        'supports' => [App\Models\BlogPost::class],
        'marking_store' => [
            'property' => 'state',
            'type' => 'single_state',
            'class' => EloquentMarkingStore::class,
        ],
        'places' => ['draft', 'pending_for_review', 'approved', 'rejected', 'published', 'archived'],
        'transitions' => [
            'submit' => [
                'from' => 'draft',
                'to' => 'pending_for_review',
                'metadata' => [
                    'guard' => 'authenticated and subject.isOwnedBy(user)',
                ],
            ],
            'approve' => [
                'from' => 'pending_for_review',
                'to' => 'approved',
            ],
            'reject' => [
                'from' => 'pending_for_review',
                'to' => 'rejected',
            ],
            'publish' => [
                'from' => 'approved',
                'to' => 'published',
            ],
            'archive' => [
                'from' => ['draft', 'rejected'],
                'to' => 'archived',
            ],
        ],
    ]
    
];
```
Currenty these variables/objects were injected into Symfony Expression Language.

- "subject" is the Eloquent model which is subject of a workflow
- "user" is authenticated user.
- "authenticated" boolean, true if user was authenticated.

So you can call any method on the injected object.

## Todo
I have a plan to prover document role for user. For example, some users may be assign as "reviewer" or "approver" for Eloquent model. So we can use something like subject.hasActorRole('reviewer') or subject.canBeReviewedBy(user). Any suggestion is welcome.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Prasit Gebsaap](https://github.com/soap)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
