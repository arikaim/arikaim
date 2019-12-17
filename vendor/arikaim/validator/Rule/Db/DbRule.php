<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator\Rule\Db;

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
     * @var Model
     */
    protected $model;

    /**
     * Constructor
     *
     * @param array $params 
     */
    public function __construct($params = []) 
    {
        parent::__construct($params);
        $this->model = Model::create($this->params->get('model'),$this->params->get('extension',null));      
    }

    /**
     * Return filter type
     *
     * @return int
     */
    public function getType()
    {       
        return FILTER_CALLBACK;
    }
}
