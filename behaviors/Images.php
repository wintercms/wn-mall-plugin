<?php

namespace Winter\Mall\Behaviors;

use Winter\Storm\Support\Collection;
use Winter\Storm\Database\ModelBehavior;
use Winter\Mall\Models\ImageSet;
use System\Models\File;

class Images extends ModelBehavior
{
    /**
     * Returns the first available image.
     *
     * @return File
     */
    public function getImageAttribute()
    {
        return optional($this->model->main_image_set_images)->first();
    }

    /**
     * Returns the first available image. Alias of get_image_attribute
     *
     * @return File
     */
    public function getMainImageAttribute()
    {
        return $this->getImageAttribute();
    }

    /**
     * Return all images except the main image.
     *
     * @return Collection
     */
    public function getImagesAttribute()
    {
        return optional($this->model->main_image_set_images)->slice(1);
    }

    /**
     * Returns all images of the main image set.
     */
    public function getMainImageSetImagesAttribute()
    {
        return optional($this->model->main_image_set)->images;
    }

    /**
     * Return all available images.
     *
     * @return File
     */
    public function getAllImagesAttribute()
    {
        return $this->model->main_image_set_images;
    }

    /**
     * Returns the main image set.
     */
    public function getMainImageSetAttribute()
    {
        if ( ! $this->model->image_sets) {
            return null;
        }

        return $this->model->image_sets instanceof ImageSet
            ? $this->model->image_sets
            : optional($this->model->image_sets->sortByDesc('is_main_set'))->first();
    }
}
