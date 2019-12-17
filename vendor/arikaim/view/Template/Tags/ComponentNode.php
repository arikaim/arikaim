<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View\Template\Tags;

use Twig\Compiler;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;
use Twig\Node\SetNode;

/**
 * Component tag node
 */
class ComponentNode extends Node implements NodeOutputInterface
{
    /**
     * Constructor
     *
     * @param Node $body
     * @param array $params
     * @param integer $line
     * @param string $tag
     */
    public function __construct(Node $body, $params = [], $line = 0, $tag = 'component')
    {
        parent::__construct(['body' => $body],$params,$line,$tag);
    }

    /**
     * Compile node
     *
     * @param Compiler $compiler
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);
        $componentName = $this->getAttribute('name');
        $params = $this->getAttribute('params');
        $exportedParams = var_export($params, true);

        $count = count($this->getNode('body'));
        $compiler->write("\$componentName = '$componentName';")->raw(PHP_EOL);
        $compiler->write("\$params = $exportedParams;")->raw(PHP_EOL);
        $compiler->write("\$context = array_merge(\$context,\$params);")->raw(PHP_EOL);
        $compiler->write('ob_start();')->raw(PHP_EOL);
        $compiler->subcompile($this->getNode('body'),true);
        $compiler->write("\$context['content'] = ob_get_clean();")->raw(PHP_EOL);
     
        for ($i = 0; ($i < $count); $i++) {
            $item = $this->getNode('body')->getNode($i);         
            if ($item instanceof SetNode) {
                $compiler->subcompile($item,true);
            }          
        }
        $compiler->write('echo $this->env->getExtension("Arikaim\\Core\\View\\Template\\Extension")->loadComponent($componentName,$context);' . PHP_EOL);
    }
}
