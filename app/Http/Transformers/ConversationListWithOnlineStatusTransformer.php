<?php

namespace App\Http\Transformers;

use App\Models\Visitor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Vinkla\Hashids\Facades\Hashids;

/**
 * 会话列表转换器
 */
class ConversationListWithOnlineStatusTransformer extends ConversationListTransformer
{
    /**
     * 转换
     *
     * @param \App\Models\Conversation $item
     * @return \App\Models\Conversation
     */
    public function transform($item)
    {
        $url = env('LARAEVL_ECHO_SERVER_API') . '/apps/' . env('LARAEVL_ECHO_SERVER_APPID') . '/channels/presence-conversation.' . $item->public_id . '/users?auth_key=' . env('LARAEVL_ECHO_SERVER_APPKEY');
        $item = parent::transform($item);
        $item->online_status = false;

        $status = Redis::connection('echo')->get('presence-conversation.' . $item->public_id . ':members');
        if ($status) {
            $status = json_decode($status, true);

            foreach (collect($status)->pluck('user_info.id') as $public_id) {
                list($id, $type) = Hashids::decode($public_id);
                if ($type == crc32(Visitor::class)) {
                    $item->online_status = true;
                    break;
                }
            }
        }

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
            'geo',
            'online_status',
        ]);

        return $item;
    }
}
