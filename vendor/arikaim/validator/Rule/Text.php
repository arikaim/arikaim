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
 * Text field rule 
 */
class Text extends Rule
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
     * @param mixed $value
     * @return boolean
     */
    public function validate($value): bool 
    {
        $errors = 0;
        $min = $this->params->get('min',null);     
        $max = $this->params->get('max',null);

        if (empty($min) == false) {           
            if (\strlen((string)$value) < $min) {
                $this->setError('TEXT_MIN_LENGHT_ERROR');
                $errors++;
            }
        }

        if (empty($max) == false) {   
            if (\strlen((string)$value) > $max) {
                $this->setError('TEXT_MAX_LENGHT_ERROR');
                $errors++;
            }
        }
        
        return ($errors == 0);
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
