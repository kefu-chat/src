<?php

namespace App\Models;

class DeletedUser extends User
{
    protected $table = 'users';

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), parent::decodePublicId($value, false))->first();
    }
}
