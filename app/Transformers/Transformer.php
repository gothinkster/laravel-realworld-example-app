<?php

namespace App\Transformers;

use App\Paginate\Paginator;
use Illuminate\Support\Collection;

abstract class Transformer
{
    protected $resourceName = 'data';

    public function collection(Collection $data)
    {
        return [
            str_plural($this->resourceName) => $data->map([$this, 'transform'])
        ];
    }

    public function item($data)
    {
        return [
            $this->resourceName => $this->transform($data)
        ];
    }

    public function paginate(Paginator $paginator)
    {
        $resourceName = str_plural($this->resourceName);

        $countName = str_plural($this->resourceName) . 'Count';

        $data = [
            $resourceName => $paginator->getData()->map([$this, 'transform'])
        ];

        return array_merge($data, [
            $countName => $paginator->getTotal()
        ]);
    }

    public abstract function transform($data);
}
