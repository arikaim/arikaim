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

/**
 * Markdown node
 */
class MdNode extends Node implements NodeOutputInterface
{
    /**
     * Constructor
     *
     * @param Node $body
     * @param array $params
     * @param integer $line
     * @param string $tag
     */
    public function __construct(Node $body, $params = [], $line = 0, $tag = 'md')
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
        $compiler->addDebugInfo($this)    
            ->write('ob_start();' . PHP_EOL)
            ->subcompile($this->getNode('body'))
            ->write('$content = ob_get_clean();' . PHP_EOL)
            ->write('preg_match("/^\s*/", $content, $matches);' . PHP_EOL)
            ->write('$lines = explode("\n", $content);' . PHP_EOL)
            ->write('$content = preg_replace(\'/^\' . $matches[0]. \'/\', "", $lines);' . PHP_EOL)
            ->write('$content = join("\n", $content);' . PHP_EOL)
            ->write('echo $this->env->getExtension("Arikaim\\Core\\View\\Template\\Extension")->parseMarkdown($content,$context);' . PHP_EOL);
    }
}
