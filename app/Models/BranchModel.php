<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

abstract class BranchModel extends Model
{
    use BelongsToBranch;

    protected $guarded = [];
}
