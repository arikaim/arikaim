<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Price;

/**
 * Price relation table trait
*/
trait PriceRelation 
{  
    /**
     * Get price list class
     *
     * @return string|null
     */
    public function getPriceListClass(): ?string
    {
        return $this->priceListClass ?? null;
    }
    
    /**
     * Return true if item is free
     *
     * @return boolean
     */
    public function getIsFreeAttribute()
    {
        return $this->isFree();
    }

    /**
     * Return true if product is free
     *
     * @return boolean
     */
    public function isFree(): bool
    {
        foreach ($this->prices as $item) {
            if ($item->prices > 0) {
                return false;
            }
         }
 
         return true;
    }

    /**
     * Price list relation
     *
     * @return Relation|null
     */
    public function prices()
    {
        return $this->hasMany($this->getPriceListClass(),'product_id');
    }

    /**
     * Get main price
     *
     * @return Model|nulll
     */
    public function mainPrice()
    {
        return $this->prices()->whereNull('key')->orWhere('key','=','price')->first();
    } 

    /**
     * Get price
     *   
     * @param string|null $key
     * @param string|null $currency
     * 
     * @return object|null
     */
    public function getPrice(?string $key = null, ?string $currency = null): ?object 
    {                
        $mainPrice = $this->mainPrice();
        if ($mainPrice == null) {
            return null;
        }

        $curencyId = $mainPrice->findCurrency($currency)->id;

        $query = (empty($key) == false) ? $this->prices()->where('key','=',$key) : $this->prices()->whereNull('key');
        $query = $query->where('currency_id','=',$curencyId);

        return $query->first();
    }

    /**
     * Get price value
     *
     * @param string|null $key
     * @param string|null $currency
     * 
     * @return float|null
     */
    public function getPriceValue(?string $key = null, ?string $currency = null): ?float 
    {
        $model = $this->getPrice($key,$currency);

        return ($model != null) ? (float)$model->price : null;
    }
}
