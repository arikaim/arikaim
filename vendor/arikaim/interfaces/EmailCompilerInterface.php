<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Compile email html code
 */
interface EmailCompilerInterface 
{  
    /**
     * Compile email html code
     *
     * @param string $htmlCode
     * @param string $cssCode
     * @return string
     */
    public function compile(string $htmlCode, string $cssCode): string;
}
