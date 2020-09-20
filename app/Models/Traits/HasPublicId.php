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
        return parent::resolveRouteBinding(static::decodePublicId($value));
    }

    /**
     * 路由 KEY
     * @return string
     */
    public function getRouteKey()
    {
        return $this->public_id;
    }

    /**
     * 查找
     * @param string $value
     * @return self|static
     */
    public static function findPublicId($value)
    {
        return parent::find(static::decodePublicId($value));
    }

    /**
     * 查找
     * @param string $value
     * @return self|static
     */
    public static function findPublicIdOrFail($value)
    {
        return parent::findOrFail(static::decodePublicId($value));
    }
    
    /**
     * 解密 public_id
     *
     * @param string $public_id
     * @return int
     */
    protected static function decodePublicId($public_id)
    {
        $decode = Hashids::decode($public_id);
        if (Arr::last($decode) != crc32(static::class)) {
            abort(403, '路由 ID 拼错了， CRC32校验失败: ' . static::class);
        }
        return Arr::first($decode);
    }
}
