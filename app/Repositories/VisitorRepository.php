<?php

namespace App\Repositories;

use App\Models\Visitor;

class VisitorRepository
{
    public function init($institution, $unique_id, $name, $email, $phone, $avatar, $memo, $address)
    {
        $visitor = Visitor::where([
            'institution_id' => $institution->id,
            'unique_id' => $unique_id,
        ])->first();

        if (!$visitor) {
            $visitor = new Visitor([
                'unique_id' => $unique_id,
            ]);
            $visitor->institution()->associate($institution);
        }
        $visitor->fill([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'avatar' => $avatar,
            'memo' => $memo,
            'address' => $address,
        ]);
        $visitor->save();

        return $visitor;
    }
}
