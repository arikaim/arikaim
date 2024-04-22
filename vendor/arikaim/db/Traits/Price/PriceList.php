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

use Arikaim\Core\Utils\Uuid;

/**
 * Price list table trait
*/
trait PriceList 
{  
    /**
     * Create price
     *
     * @param integer $productId
     * @param string $key
     * @param string|null|int $currency
     * @param float $price
     * @return Model|false
     */
    public function createPrice(int $productId, $key, float $price, ?string $currency = null)
    {
        if ($this->hasPrice($productId,$key) == true) {     
            return false;
        }
        
        $curencyId = $this->findCurrency($currency)->id;

        return $this->create([
            'product_id'  => $productId,
            'currency_id' => $curencyId,
            'uuid'        => Uuid::create(),          
            'key'         => $key,
            'price'       => $price     
        ]);      
    }

    /**
     * Get price
     *
     * @param integer $productId
     * @param string $key
     * @param string|int|null $currency
     * @return Model|null
     */
    public function getPrice(int $productId, ?string $key = null, ?string $currency = null): ?object
    {      
        return $this->priceQuery($productId,$key,$currency)->first();                          
    }

    /**
     * Get price list query
     *
     * @param integer $productId
     * @return Builder
     */
    public function scopePriceQuery($query, int $productId, ?string $key = null, ?string $currency = null)
    {
        $curencyId = $this->findCurrency($currency)->id;

        $query = $query
            ->where('product_id','=',$productId)
            ->where('currency_id','=',$curencyId);

        return (empty($key) == true) ? $query : $query->where('key','=',$key);
    }

    /**
     * Return true if price exist
     *
     * @param integer $productId
     * @param string|null $key
     * @param string|int|null $currency
     * @return boolean
     */
    public function hasPrice(int $productId, ?string $key = null, ?string $currency = null): bool
    {
        return ($this->getPrice($productId,$key,$currency) !== null);       
    }

    /**
     * Save price
     *
     * @param integer $productId
     * @param string|null $key
     * @param float $price
     * @param string|null $currency
     * @return mixed
     */
    public function savePrice(int $productId, float $price, ?string $key = null, ?string $currency = null) 
    {
        if ($this->hasPrice($productId,$key,$currency) == false) {          
            return $this->createPrice($productId,$key,$price,$currency);
        }
      
        return $this->priceQuery($productId,$key,$currency)->update([
            'price' => $price
        ]);  
    }
}
