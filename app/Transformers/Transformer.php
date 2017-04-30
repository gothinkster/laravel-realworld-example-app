<?php

namespace App\Transformers;

use App\Paginate\Paginator;
use Illuminate\Support\Collection;

abstract class Transformer
{
    /**
     * Resource name of the json object.
     *
     * @var string
     */
    protected $resourceName = 'data';

    /**
     * Transform a collection of items.
     *
     * @param Collection $data
     * @return array
     */
    public function collection(Collection $data)
    {
        return [
            str_plural($this->resourceName) => $data->map([$this, 'transform'])
        ];
    }

    /**
     * Transform a single item.
     *
     * @param $data
     * @return array
     */
    public function item($data)
    {
        return [
            $this->resourceName => $this->transform($data)
        ];
    }

    /**
     * Transform a paginated item.
     *
     * @param Paginator $paginator
     * @return array
     */
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

    /**
     * Apply the transformation.
     *
     * @param $data
     * @return mixed
     */
    public abstract function transform($data);
}
