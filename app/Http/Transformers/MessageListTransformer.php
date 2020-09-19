<?php

namespace App\Http\Transformers;

use App\Models\User;

/**
 * 消息列表转换器
 */
class MessageListTransformer extends AbstractTransformer
{
    /**
     * 转换
     *
     * @param \App\Models\Message $item
     * @return \App\Models\Message
     */
    public function transform($item)
    {
        $item->setAppends([
            'sender_type_text',
        ]);
        $item->setVisible([
            'id',
            'visitor_id',
            'conversation_id',
            'type',
            'content',
            'sender',
            'created_at',
            'updated_at',
            'sender_type_text',
            'sender_id',
        ]);

        return $item;
    }
}
