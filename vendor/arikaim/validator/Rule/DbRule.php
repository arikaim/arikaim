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

use Arikaim\Core\Db\Model;
use Arikaim\Core\Validator\Rule;

/**
 * Base class for all Db rules
 */
class DbRule extends Rule
{    
    /**
     * Model
     *
     * @var object|null
     */
    protected $model;

    /**
     * Constructor
     *
     * @param array $params 
     * @param string|null $error 
    */
    public function __construct(array $params = [], ?string $error = null) 
    {
        parent::__construct($params,$error);
        
        $this->model = Model::create($this->params->get('model'),$this->params->get('extension',null));      
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
