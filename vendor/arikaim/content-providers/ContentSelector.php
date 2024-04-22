<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content;

use Exception;

/**
 *  Content selector
 */
class ContentSelector  
{
    const CONTENT_PROVIDER_TYPE = 'content';
    const DB_MODEL_TYPE         = 'model';

    const SELECTOR_TYPES = [
        Self::CONTENT_PROVIDER_TYPE,
        Self::DB_MODEL_TYPE
    ];

    /**
     * Create content selector
     *
     * @param string $provider
     * @param string $contentType
     * @param string $keyFields
     * @param string $key
     * @param string $type
     * @return string
     */
    public static function create(
        string $provider,
        string $contentType,
        string $keyFields,
        string $key, 
        string $type = 'content'
    ): string
    {
        return $type . '>' . $provider . ',' . $contentType . ':' . $keyFields . ':' . $key; 
    }

    /**
     * Parse content selector
     *  
     *  {type} > {provider name|model name,type name|extension name} : {key_fields...} : {key_values...} 
     * 
     *  result array keys - type, provider, content_type, key_fields, key_values 
     *       
     * @param string $selector
     * @throws Exception
     * @return array|null
     */
    public static function parse(string $selector): ?array
    {      
        if (empty($selector) == true) {
            return null;
        }

        list($result['type'],$params) = \explode('>',$selector);
        if (\in_array($result['type'],Self::SELECTOR_TYPES) == false) {
            throw new Exception('Not vlaid content selector type ' . $result['type'],1);
            return null;             
        }
     
        list($provider,$keyFields,$keyValues) = \explode(':',$params);
        list($result['provider'],$result['content_type']) = \array_pad(\explode(',',$provider),2,null);

        $result['key_fields'] = \explode(',',$keyFields);
        $result['key_values'] = \explode(',',$keyValues);
       
        return $result;
    }

    /**
     * Return true if content selector is valid
     *
     * @param string $selector
     * @return boolean
     */
    public static function isValid(string $selector): bool
    {
        if (empty($selector) == true) {
            return false;
        }

        list($type,$params) = \explode('>',$selector);

        if (\in_array($type,Self::SELECTOR_TYPES) == false) {
            return false;
        }
        if (empty($params) == true) {
            return false;
        }

        return true;
    }
}
