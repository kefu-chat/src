<?php

namespace App\Http\Transformers;

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
        $item->setVisible([
            'id',
            'name',
            'email',
            'avatar',
            'title',
        ]);
        return $item;
    }
}
