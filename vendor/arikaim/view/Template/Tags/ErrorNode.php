<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
*/
namespace Arikaim\Core\View\Template\Tags;

use Twig\Compiler;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;

/**
 * Error node
 */
class ErrorNode extends Node implements NodeOutputInterface
{
    /**
     * Compile node
     *
     * @param Compiler $compiler
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);
        $message = \trim($this->getNode('message')->getAttribute('value'),'\'"');
        $compiler          
            ->write("echo '" . $message . "';\n")
            ->write('return false;' . PHP_EOL);
            
    }
}
