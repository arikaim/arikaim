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
 * Document number table trait
*/
trait DocumentNumber 
{ 
    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootDocumentNumber()
    {
        static::creating(function($model) {          
            $columnName = $model->getDocumentNumberColumn();
            if (empty($model->$columnName) == true) {  
                $model->attributes[$columnName] = $model->getNextDocumentNumber();
            }           
        });
    }

    /**
     * Get document number column
     *
     * @return string
     */
    public function getDocumentNumberColumn()
    {
        return (isset($this->documentNumberColumn) == true) ? $this->documentNumberColumn : 'document_number';
    } 

    /**
     * Get label
     *
     * @return string
     */
    public function getDocumentNumberLabel()
    {
        return (isset($this->documentNumberLabel) == true) ? $this->documentNumberLabel : '';
    } 

    /**
     * Get next document number
     *
     * @param string|integer|null $id
     * @return integer
     */
    public function getNextDocumentNumber($id = null)
    {
        $columnName = $this->getDocumentNumberColumn();

        $model = (empty($id) == true) ? $this : $this->findById($id);
        $max = $model->where('id','=',$model->id)->max($columnName);

        return (empty($max) == true ) ? 0 : ($max + 1); 
    }

    /**
     * Get document number
     *
     * @param string $prefix
     * @return string|null
     */
    public function getDocumentNumber($prefix = '')
    {
        $columnName = $this->getDocumentNumberColumn();
        $label = $this->getDocumentNumberLabel();
        $documentNumber = (isset($this->attributes[$columnName]) == true) ? $this->attributes[$columnName] : null;
     
        return (empty($documentNumber) == false) ? sprintf($label . '1%05d' . $prefix,$documentNumber) : null;       
    }   
}
