<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Document;

/**
 * Document table trait
*/
trait Document 
{ 
    /**
     * Get document model class
     *
     * @return string|null
     */
    public function getDocumentItemsClass(): ?string
    {
        return $this->documentItemsModel ?? null;
    }

    /**
     * Get document items count
     *
     * @return integer
     */
    public function getItemsCount(): int
    {
        $count = $this->items->count();
        
        return (empty($count) == true) ? 0 : $count;
    }

    /**
     * Return true if document is empty
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return ($this->getItemsCount() == 0);
    }

    /**
     * Save document item
     *
     * @param integer $productId
     * @param array   $data
     * @return object|null
     */
    public function saveItem(int $productId, array $data): ?object
    {
        $item = $this->items->where('product_id','=',$productId)->first();
        if ($item == null) {
            return $this->create($data);
        }
    
        $item->update($data);

        return $item;
    }

    /**
     * Delete document item
     *
     * @param  string|int $id
     * @return boolean
     */
    public function deleteItem($id): bool 
    {
        $items = $this->items();
        if ($items == null) {
            return true;
        }

        $item = $items->where(function ($query) use($id) {
            return $query->where('uuid','=',$id);          
        });

        return (bool)$item->delete();
    }

    /**
     * Get external document 
     *
     * @param string $externalId
     * @param string $driverName
     * @return Model|null
     */
    public function getExternal(string $externalId, string $driverName): ?object
    {
        return $this
            ->where('external_id','=',$externalId)
            ->where('api_driver','=',$driverName)->first();
    }

    /**
     * Return true if external document exists
     *
     * @param string $externalId
     * @param string $driverName
     * @return boolean
     */
    public function hasExternal(string $externalId, string $driverName): bool
    {
        return ($this->getExternal($externalId,$driverName) !== null);       
    }
    
    /**
     * Document items relation
     *
     * @return Relation|null
     */
    public function items()
    {
        $class = $this->getDocumentItemsClass();
      
        return (empty($class) == true) ? null : $this->hasMany($class,'document_id')->without('document');
    }

    /**
     * Items array
     *
     * @return array
     */
    public function itemsToArray(): array
    {
        $items = $this->items();

        return (empty($items) == true) ? [] : $items->get()->toArray();
    } 

    /**
     * Update document total
     *
     * @return boolean
     */
    public function updateTotals(): bool
    {
        return (bool)$this->update([
            'total'     => $this->getTotal(),
            'sub_total' => $this->getSubTotal()
        ]);
    }

    /**
     * Get document total
     *
     * @return float
     */
    public function getSubTotal(): float
    {
        $items = $this->items()->get();
        $total = 0.00;

        foreach($items as $item) {              
            $total += $item->getItemTotal();
        }

        return $total; 
    }

   
    /**
     * Get total document fees
     *
     * @return float
     */
    public function getTotal(): float
    {
        return $this->getSubTotal() + $this->getTotalFees();
    }

    /**
     * Get total document fees
     *
     * @return float
     */
    public function getTotalFees(): float
    {
        return 0.00;
    }

    /**
     * sub_total attribute
     *
     * @return float
     */
    public function getSubTotalAttribute()
    {
        return $this->getSubTotal();
    }

    /**
     * total_fees attribute
     *
     * @return float
     */
    public function getTotalFeesAttribute()
    {
        return $this->getTotalFees();
    }

    /**
     * total attribute
     *
     * @return float
     */
    public function getTotalAttribute()
    {
        return $this->getTotal();
    }
}
