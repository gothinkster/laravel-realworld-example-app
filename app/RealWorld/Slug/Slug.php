<?php

namespace App\RealWorld\Slug;

use Illuminate\Database\Eloquent\Model;

class Slug
{
    /**
     * Eloquent model used for example for uniqueness
     *
     * @var Model
     */
    protected $model;

    /**
     * Banned values for slug generation
     *
     * @var array
     */
    protected $banned = [];

    /**
     * Initial value to slugify
     *
     * @var string
     */
    private $initialValue;

    /**
     * Separator use to generate slugs
     *
     * @var string
     */
    const SEPARATOR = '-';

    /**
     * Slug constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        $this->initialValue = $value;
    }

    /**
     * Generate a unique slug
     *
     * @return string
     */
    public function generate()
    {
        $slug = str_slug($this->initialValue, static::SEPARATOR);

        $notAllowed = $this->getSimilarSlugs($slug)->merge($this->banned);

        if ($notAllowed->isEmpty() || !$notAllowed->contains($slug)) {
            return $slug;
        }

        $suffix = $this->generateSuffix($slug, $notAllowed);

        return $slug . static::SEPARATOR . $suffix;

    }

    /**
     * Generate suffix for unique slug
     *
     * @param string $slug
     * @param \Illuminate\Support\Collection $notAllowed
     * @return string
     */
    public function generateSuffix($slug, $notAllowed)
    {
        /** @var \Illuminate\Support\Collection $notAllowed */
        $notAllowed->transform(function ($item) use ($slug) {

            if ($slug == $item) {
                return 0;
            }

            return (int)str_replace($slug . static::SEPARATOR, '', $item);
        });

        return $notAllowed->max() + 1;
    }

    /**
     * Set eloquent model to check uniqueness on.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return $this
     */
    public function uniqueFor(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set array of values which are not allowed.
     *
     * @param $values
     * @return $this
     */
    public function without($values)
    {
        $this->banned = $values;

        return $this;
    }

    /**
     * Get collection of similar slugs based on database
     *
     * @param $slug
     * @return \Illuminate\Support\Collection
     */
    private function getSimilarSlugs($slug)
    {
        if (!$this->model instanceof Model || !method_exists($this->model, 'getSlugColumn')) {
            return collect([]);
        }

        $slugColumn = $this->model->getSlugColumn();

        return $this->model->newQuery()
            ->where($slugColumn, $slug)
            ->orWhere($slugColumn, 'LIKE', $slug . static::SEPARATOR . '%')
            ->get()
            ->pluck($slugColumn);
    }

}