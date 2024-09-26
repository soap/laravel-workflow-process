<?php

namespace Soap\LaravelWorkflowProcess\Commands;

use Illuminate\Console\Command;

class LaravelWorkflowProcessCommand extends Command
{
    public $signature = 'laravel-workflow-process';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
