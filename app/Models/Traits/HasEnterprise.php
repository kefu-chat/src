<?php

namespace App\Models\Traits;

use App\Models\Enterprise;

/**
 * 企业的
 *
 * @property Enterprise $enterprise
 */
trait HasEnterprise
{
    /**
     * 企业
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Enterprise|\Illuminate\Database\Query\Builder
     */
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
