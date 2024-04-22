<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Access
*/
namespace Arikaim\Core\Access\Traits;

/**
 *  Auth trait
 *  For change auth id name in model:  protected $authIdColumn = 'auth id name';
*/
trait Auth 
{   
    /**
     * Return Auth id name
     *
     * @return string
     */
    public function getAuthIdName(): string
    {
        return $this->authIdColumn ?? 'id';
    }

    /**
     * Return auth id
     *
     * @return mixed
     */
    public function getAuthId()
    {
        return $this->{$this->getAuthIdName()};
    }
}
