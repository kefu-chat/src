<?php

namespace App\Http\Transformers;

abstract class AbstractTransformer
{
    /**
     * 转换
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     * @return fixed
     */
    abstract public function transform($item);
}
