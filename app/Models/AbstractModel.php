<?php

namespace App\Models;

use App\Models\Traits\SetTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
abstract class AbstractModel extends Model
{
    use SetTransformer;
}
