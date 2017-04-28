<?php

namespace App\Transformers;

class TagTransformer extends Transformer
{
    public function item($data)
    {
        return [
            'tags' => $data
        ];
    }
}