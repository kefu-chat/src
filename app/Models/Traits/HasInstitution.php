<?php

namespace App\Models\Traits;

use App\Models\Institution;

/**
 * 机构的
 *
 * @property int $institution_id 组织 ID
 * @property-read Institution $institution
 */
trait HasInstitution
{
    /**
     * 机构
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Institution|\Illuminate\Database\Query\Builder
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
