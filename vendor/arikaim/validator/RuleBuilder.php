<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator;

use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Collection\Arrays;

/**
 * Rule builder
 */
class RuleBuilder
{
    /**
     * Create rules from array
     *
     * @param array $descriptor
     * @return array
     */
    public function createRules(array $descriptor)
    {       
        $rules = [];      
        foreach ($rules as $value) {
            $rule = $this->createRule($value);
            array_push($rules,$rule);
        }

        return $rules;
    }

    /**
     * Create rule from text descriptor
     * pattern: name:param1=value|param2=value
     * 
     * @param string $descriptor
     * @param string|null $error
     * @return Arikaim\Core\Validator\Interfaces\RuleInterface
     */
    public function createRule($descriptor, $error = null)
    {
        $data = $this->parseRuleDescriptor($descriptor);
        $rule = Factory::createRule($data['class'],[$data['params']]);
        if (empty($error) == false) {
            $rule->setError($error);          
        }

        return $rule;
    }

    /**
     * Parse rule descriptor   
     * pattern: name:param1=value|param2=value
     *
     * @param array|null $descriptor
     * @return void
     */
    public function parseRuleDescriptor($descriptor)
    {
        $result = [];
        $descriptor = trim($descriptor);
        $tokens = explode(':',$descriptor);      
        $result['class'] = ucfirst($tokens[0]);

        $params = (isset($tokens[1]) == true) ? $tokens[1] : '';
        $result['params'] = $this->parseRuleParams($params);
        
        return $result;
    }
    
    /**
     * Parse rule params 
     * pattern: name:param1=value|param2=value
     *
     * @param string $params
     * @return array
     */
    public function parseRuleParams($params)
    {
        $result = [];
        $tokens = explode('|',$params);
        foreach ($tokens as $value) {
            $param = $this->parseRuleParam($value);
            $result[$param['name']] = $param['value'];      
        }

        return $result;
    }

    /**
     * parse rule parameter
     * pattern: name:param1 | name
     * 
     * @param string $param
     * @return array
     */
    public function parseRuleParam($param)
    {
        $tokens = explode('=',$param);
        $value = (isset($tokens[1]) == false) ? true : $tokens[1];

        if (count(explode(',',$value)) > 1) {
            $value = Arrays::toArray($value,',');
        }
        
        return ['name' => $tokens[0],'value' => $value];
    }

    /**
     * Create rule
     *
     * @param string $name
     * @param array|null $args
     * @return Arikaim\Core\Validator\Interfaces\RuleInterface
     */
    public function __call($name, $args)
    {  
        return Factory::createRule($name,$args);       
    }
}
