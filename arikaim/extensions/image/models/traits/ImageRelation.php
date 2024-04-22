<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image\Models\Traits;

use Arikaim\Extensions\Image\Models\Image;

/**
 * Image relation trait
 *      
*/
trait ImageRelation 
{    
    /**
     * Get image column relation name
     *
     * @return string
     */
    public function getImageColumn(): string
    {
        return $this->imageColumn ?? 'image_id';
    }

    /**
     * Remove image relation id
     *
     * @return boolean
     */
    public function unsetImage(): bool
    {
        return $this->setImage(null);
    }

    /**
     * Set image relation id
     *
     * @param integer|null $imageId
     * @return boolean
     */
    public function setImage(?int $imageId): bool
    {
        return ($this->update([ $this->getImageColumn() => $imageId ]) !== false);
    }

    /**
     * Get image relation
     *
     * @return Relation|null
     */
    public function image()
    {
        return $this->belongsTo(Image::class,$this->getImageColumn());
    }

    /**
     * Return true if image relation exist
     *
     * @return boolean
     */
    public function hasImage(): bool
    {
        $imageId = $this->attributes[$this->getImageColumn()] ?? null;

        return (empty($imageId) == true) ? false : ($this->image !== null);
    }
}
