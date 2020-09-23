<?php

namespace App\Models\Traits;

/**
 * 转换方法
 */
trait SetTransformer
{
    /**
     * 转换
     *
     * @param string $class
     * @return static
     */
    public function setTransformer($class)
    {
        return (new $class)->transform($this);
    }
}
