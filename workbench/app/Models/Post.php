<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ZeroDaHero\LaravelWorkflow\Traits\WorkflowTrait;

class Post extends Model
{
    use HasFactory;
    use WorkflowTrait;
}
