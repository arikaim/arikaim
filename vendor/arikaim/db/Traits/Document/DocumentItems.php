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

use Closure;

/**
 * Document items table trait
*/
trait DocumentItems 
{ 
    /**
     * Document relation
     *
     * @return Relation|null
     */
    public function document()
    {
        $class = $this->documentClass ?? null;
      
        return (empty($class) == true) ? null : $this->belongsTo($class,'document_id')->without('items');
    }

    /**
     * Get item total attribute
     *
     * @return string
     */
    public function getItemTotalAttributeName(): string
    {
        return $this->itemTotalColumn ?? 'total';
    }
    
    /**
     * Set model events
     *
     * @return void
     */
    public static function bootDocumentItems()
    {
        static::updating(function($model) {      
            $model->attributes[$model->getItemTotalAttributeName()] = $model->getItemTotal();              
        });

        self::updated(function($model) {
            $model->updateDocumentTotals();  
        });

        static::creating(function($model) {  
            $model->attributes[$model->getItemTotalAttributeName()] = $model->getItemTotal();          
        });

        self::created(function($model) {
            $model->updateDocumentTotals();    
        });

        self::deleted(function($model) {
            $model->updateDocumentTotals();
        });
    }

    /**
     * Updaet document totals
     *
     * @return boolean
     */
    public function updateDocumentTotals(): bool
    {
        // update document
        $document = $this->document()->first();

        return ($document == null) ? false : $document->updateTotals();
    }

    /**
     * Get item calc closure
     *
     * @return Closure
     */
    public function getItemCalc()
    {
        return (\is_callable($this->calcItemTotal) == true) ? $this->calcItemTotal : function($item) {
            return (float)($item->price * $item->qty);
        };
    }

    /**
     * Get document total
     *
     * @return float
     */
    public function getItemTotal(): float
    {
        return (float)$this->getItemCalc()($this);    
    }

    /**
     * item_total attribute
     *
     * @return float
     */
    public function getItemTotalAttribute()
    {
        return $this->getItemTotal();
    }

    /**
     * Return true if item exist
     *
     * @param integer $documentId
     * @param integer $productId
     * @return boolean
     */
    public function hasItem(int $documentId, int $productId): bool
    {
        return ($this->itemsQuery($documentId,$productId)->first() !== null);        
    }

    /**
     * Get item
     *
     * @param integer $documentId
     * @param integer $productId
     * @return Model|null
     */
    public function getItem(int $documentId, int $productId): ?object
    {
        return $this->itemsQuery($documentId,$productId)->first();
    } 

    /**
     * Save item
     *
     * @param integer $documentId
     * @param integer $productId
     * @param int  $qty
     * @param float|null   $price
     * @param string|null $productName
     * @return Model
     */
    public function saveItem(int $documentId, int $productId, $qty, ?float $price, ?string $productName = null): ?object
    {
        $item = $this->getItem($documentId,$productId);
        $data = [
            'document_id' => $documentId,
            'product_id'  => $productId,
            'title'       => $productName,
            'qty'         => $qty,
            'price'       => $price ?? 0.00
        ];

        if ($item == null) {
            return $this->create($data);
        }
        // add to existing 
        $data['qty'] = $item['qty'] + $qty;
        $data['price'] = (empty($price) == true) ? $item['price'] : $price;

        $item->update($data);

        return $item;
    }

    /**
     * Items query
     *
     * @param Builder $query
     * @param integer|null $documentId
     * @return Builder
     */
    public function scopeItemsQuery($query, ?int $documentId, ?int $productId = null)
    {
        if (empty($documentId) == false) {
            $query = $query->where('document_id','=',$documentId);
        }
        
        if (empty($productId) == false) {
            $query = $query->where('product_id','=',$productId);
        }

        return $query;
    }
}
