<?php

namespace App\Repositories;

use App\Models\Institution;
use App\Models\Visitor;

class VisitorRepository
{
    /**
     * 创建访客
     *
     * @param Institution $institution
     * @param string|null $unique_id
     * @param string|null $name
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $avatar
     * @param string|null $memo
     * @param string|null $address
     * @return Visitor
     */
    public function init(Institution $institution, $unique_id, $name, $email, $phone, $avatar, $memo, $address)
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
