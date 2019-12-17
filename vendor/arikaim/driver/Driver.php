<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Driver;

use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Driver\Traits\Driver as DriverTrait;

/**
 * Driver base class
*/
class Driver implements DriverInterface
{
    use DriverTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
    }
}
