<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

/**
 * Update position field
 * Change default position  attribute in model
 *     protected $positionColumnName = 'attribute name';
*/
trait Position 
{    
    /**
     * Init model events
     *
     * @return void
     */
    public static function bootPosition()
    {
        static::creating(function($model) {   
            $model = self::setLastPosition($model);
        });
    }
    
    /**
     * Get position column name
     *
     * @return string
     */
    protected function getPositionAttributeName()
    {
        return (isset($this->positionColumnName) == true) ? $this->positionColumnName : 'position';
    }

    /**
     * Set model position value
     *
     * @param Model $model
     * @return Model
     */
    private static function setLastPosition($model)
    {   
        $column = $model->getPositionAttributeName();
       
        if (empty($model->$column) == true) {      
            $model->$column = $model->max($column) + 1;
        }      

        return $model;
    }

    /**
     * Move to first position
     *
     * @return Model
     */
    public function moveFirst()
    {
        $column = $this->getPositionAttributeName();
        $first = static::query()->limit(1)->ordered()->first();

        if ($this->id == $first->id) {
            return $this;
        }
        
        $this->$column = $first->$column;
        $this->save();

        $this->where($this->getKeyName(), '!=', $this->id)->increment($column);

        return $this;
    }

    /**
     * Move to last position
     *
     * @return Model
     */
    public function moveLast()
    {
        $column = $this->getPositionAttributeName();
        $max = $this->getMaxPosition();

        if ($this->$column === $max) {
            return $this;
        }

        $position = $this->$column;
        $this->$column = $max;

        $this->save();
        static::query()
            ->where($this->getKeyName(), '!=', $this->id)
            ->where($column, '>', $position)
            ->decrement($column);

        return $this;
    }

    /**
     * Shift position up or down
     *
     * @param Model $target
     * @return Model
     */
    public function shiftPosition($target)
    {
        $column = $this->getPositionAttributeName();
        $position = $target->$column;
        $currentPosition = $this->$column;

        $this->$column = null;
        $this->save();

        if ($this->$column === $position) {
            return $this;
        }

        if ($currentPosition < $position) {
            // shift up
            static::query()
            ->where($this->getKeyName(), '!=', $this->id)
            ->where($column, '<=', $position)
            ->where($column, '>=', $currentPosition)
            ->orderBy($column,'asc')->decrement($column);
        } else {
            // shift down
            static::query()
            ->where($this->getKeyName(), '!=', $this->id)
            ->where($column, '>=', $position)
            ->where($column, '<=', $currentPosition)
            ->orderBy($column,'desc')->increment($column);
        }

        $this->$column = $position;
        $this->save();

        return $this;
    }
    
    /**
     * Swap positions
     *
     * @param Model $model
     * @return object
     */
    public function swapPosition($model)
    {
        $column = $this->getPositionAttribute();
        $position = $model->$column;

        // set to null avoid unique key issue
        $model->$column = null;
        $model->save();

        $model->$column = $this->$column;

        $this->$column = $position;
        $this->save();

        $model->save();
        
        return $this;
    }

    /**
     * Get model with max position 
     *
     * @return Model
     */
    public function getMaxPosition()
    {
        return (int)static::query()->max($this->getPositionAttribute());
    }
}
