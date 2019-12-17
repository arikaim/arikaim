<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Validator\Interfaces;

/**
 * Validation rule interface
 */
interface RuleInterface
{    
    /**
     * Get rule type 
     *
     * @return void
     */
    public function getType(); 

    /**
     * Executed if rule type is FILTER_CALLBACK
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value); 

    /**
     * Return rule params
     *
     * @return Collection
     */
    public function getParams();

    /**
     * Retrun rule fixed field name or null if not used
     *
     * @return string|null
     */
    public function getFieldName();
}
