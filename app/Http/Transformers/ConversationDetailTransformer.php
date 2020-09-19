<?php

namespace App\Http\Transformers;

/**
 * 会话列表转换器
 */
class ConversationDetailTransformer extends AbstractTransformer
{
    /**
     * 转换
     *
     * @param \App\Models\Conversation $item
     * @return \App\Models\Conversation
     */
    public function transform($item)
    {
        if ($item->user) {
            $item->user->setVisible([
                'id',
                'name',
                'email',
                'avatar',
            ]);
        }
        $item->visitor->setVisible([
            'id',
            'unique_id',
            'name',
            'email',
            'phone',
            'memo',
            'avatar',
        ]);
        $item->setVisible([
            'id',
            'visitor_id',
            'ip',
            'url',
            'first_reply_at',
            'last_reply_at',
            'created_at',
            'updated_at',
            'visitor',
            'user',
        ]);

        return $item;
    }
}
