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
 * HtmlTags rule 
 */
class HtmlTags extends Rule
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

        $this->setDefaultError('TEXT_NOT_VALID_ERROR');
    }

    /**
     * Verify if value is valid
     *
     * @param string $value
     * @return boolean
     */
    public function validate($value): bool 
    {      
        $tags = $this->params->get('tags',null);
     
        return ($value == \strip_tags($value,$tags));
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
