<?php

namespace App\Models\Traits;

use Illuminate\Support\Arr;
use Vinkla\Hashids\Facades\Hashids;

/**
 * 机构的
 *
 * @property-read string $public_id
 * @property int $id
 */
trait HasPublicId
{
    protected $hashed_id;

    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->id = $this->public_id;
        return parent::jsonSerialize();
    }

    /**
     * 哈希ID
     * @return stirng
     */
    public function getPublicIdAttribute()
    {
        if (!$this->hashed_id) {
            $this->hashed_id = Hashids::encode([$this->id, crc32(static::class)]);
        }
        return $this->hashed_id;
    }

    /**
     * 路由绑定
     * {@inheritdoc}
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $decode = Hashids::decode($value);
        if (Arr::last($decode) != crc32(static::class)) {
            abort(403, '路由 ID 拼错了， CRC32校验失败: ' . static::class);
        }

        return parent::resolveRouteBinding(Arr::first($decode));
    }

    /**
     * 路由 KEY
     * @return string
     */
    public function getRouteKey()
    {
        return $this->public_id;
    }
}
