<?php

namespace App\Interfaces;

interface ShoudWebpush
{
    /**
     * 获取webpush的通知对象
     *
     * @return array
     */
    public function getWebpushNotification();

    /**
     * 获取webpush的接收人
     *
     * @return array<\App\Models\Traits\Pushable|\App\Models\User|\App\Models\Visitor>
     */
    public function getWebpushSubscriber();
}
