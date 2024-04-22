<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator\Rule;

use Arikaim\Core\Validator\Rule;

/**
 * Check if value is in list
 */
class CheckList extends Rule
{
    /**
     * Constructor
     *
     * @param array $params 
     * @param string|null $error 
    */
    public function __construct(array $params = [], ?string $error = null) 
    {
        parent::__construct($params,$error);
    }

    /**
     * Validate value
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool 
    {
        $items = $this->params->get('items',[]);
        if (\in_array($value,$items,false) == false) {        
            $this->setErrorParams($items);  
            return false;         
        } 
        
        return true;
    } 

    /**
     * Return filter type
     *
     * @return mixed
     */
    public function getType()
    {       
        return FILTER_CALLBACK;
    }
}
