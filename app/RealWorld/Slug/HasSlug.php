<?php

namespace App\RealWorld\Slug;

trait HasSlug
{
    /**
     * Adding or updating slug when attribute to "slugify" is set.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if($key == $this->getSlugSourceColumn()) {
            $slug = (new Slug($value))
                ->uniqueFor($this)
                ->without($this->getBannedSlugValues())
                ->generate();

            $this->attributes[$this->getSlugSourceColumn()] = $value;
            $this->attributes[$this->getSlugColumn()] = $slug;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Get the attribute name to slugify.
     *
     * @return string
     */
    abstract public function getSlugSourceColumn();

    /**
     * Get the name of the slug column
     *
     * @return string
     */
    public function getSlugColumn()
    {
        return 'slug';
    }

    /**
     * Get list of values wich are not allowed
     *
     * @return array
     */
    public function getBannedSlugValues()
    {
        return [];
    }
}