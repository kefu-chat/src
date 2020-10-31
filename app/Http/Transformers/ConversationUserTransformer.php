<?php

namespace App\Http\Transformers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * 会话客服转换器
 */
class ConversationUserTransformer extends AbstractTransformer
{
    /**
     * 转换
     *
     * @param \App\Models\User $item
     * @return \App\Models\User
     */
    public function transform($item)
    {
        $item->id = $item->public_id;
        $item->institution_id = $item->institution->public_id;
        $item->enterprise_id = $item->enterprise->public_id;
        $item->user_type_text = Str::lower(Arr::last(explode('\\', get_class($item))));

        $item->setVisible([
            'id',
            'name',
            'email',
            'avatar',
            'title',
            'institution_id',
            'enterprise_id',
            'user_type_text',
        ]);
        return $item;
    }
}
