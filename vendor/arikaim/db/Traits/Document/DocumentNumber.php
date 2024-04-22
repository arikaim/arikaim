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
 * Document number table trait
 * 
 *  Change document number column
 *      protected $documentNumberColumn = 'column name'
 * 
 *  Change document number label
 *      protected $documentNumberLabel = ' label '
*/
trait DocumentNumber 
{ 
    /**
     * Default document number column
     *
     * @var string
     */
    protected static $DEFAULT_DOCUMENT_NUMBER_COLUMN = 'document_number';

    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootDocumentNumber()
    {
        static::creating(function($model) {          
            $columnName = $model->documentNumberColumn ?? static::$DEFAULT_DOCUMENT_NUMBER_COLUMN;
            if (empty($model->$columnName) == true) {  
                $model->attributes[$columnName] = $model->getNextDocumentNumber();
            }           
        });
    }

    /**
     * Get document number unique index columns
     *
     * @return string|null
     */
    public function getDocumentNumberUniqueIndex(): ?string
    {
        return $this->documentNumberUniqueIndex ?? null;
    } 

    /**
     * Get next document number
     *     
     * @param mixed|null $filterColumnValue
     * @return integer
     */
    public function getNextDocumentNumber($filterColumnValue = null): int
    {       
        $indexColumn = $this->getDocumentNumberUniqueIndex();
             
        if (empty($indexColumn) == false) {
            $filterColumnValue = (empty($filterColumnValue) == true) ? $this->{$indexColumn} : $filterColumnValue;
            $model = $this->where($indexColumn,'=',$filterColumnValue);
        } else {
            $model = $this;     
        }
     
        $max = $model->max($this->documentNumberColumn ?? static::$DEFAULT_DOCUMENT_NUMBER_COLUMN);

        return (empty($max) == true) ? 1 : ($max + 1); 
    }

    /**
     * Return true if document number is valid
     *
     * @param integer|null $documentNumber
     * @param mixed|null $filterColumnValue
     * @return boolean
     */
    public function isValidDocumentNumber(?int $documentNumber = null, $filterColumnValue = null): bool
    {
        $columnName = $this->documentNumberColumn ?? static::$DEFAULT_DOCUMENT_NUMBER_COLUMN;
        $columnValue = (isset($this->attributes[$columnName]) == true) ? $this->attributes[$columnName] : $documentNumber;

        $indexColumn = $this->getDocumentNumberUniqueIndex();
        $filterColumnValue = (empty($filterColumnValue) == true) ? $this->{$indexColumn} : $filterColumnValue;

        $model = $this->where($columnName,'=',$columnValue)->where($indexColumn,'=',$filterColumnValue)->first();
        
        return ($model !== null);
    } 

    /**
     * Get document number
     *
     * @param string $prefix
     * @return string|null
     */
    public function getDocumentNumber(string $prefix = ''): ?string
    {       
        $documentNumber = (int)$this->attributes[$this->documentNumberColumn ?? static::$DEFAULT_DOCUMENT_NUMBER_COLUMN] ?? null;
     
        return (empty($documentNumber) == false) ? $this->printDocumentNumber($documentNumber,$prefix) : null;       
    }   

    /**
     * Print doc number
     *
     * @param integer|null $number
     * @param string $prefix
     * @return string
     */
    public function printDocumentNumber(?int $number = null, string $prefix = ''): string
    {
        $number = $number ?? $this->{$this->documentNumberColumn ?? static::$DEFAULT_DOCUMENT_NUMBER_COLUMN};
        return \sprintf(($this->documentNumberLabel ?? '') . '%012d' . $prefix,$number);
    }

    /**
     * Print next document number
     *
     * @param string $prefix
     * @return string
     */
    public function printNextDocumentNumber(string $prefix = ''): string
    {
        return $this->printDocumentNumber($this->getNextDocumentNumber(),$prefix);
    } 
}
