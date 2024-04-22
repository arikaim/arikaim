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

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

use Arikaim\Core\View\Template\Tags\ComponentNode;

/**
 * Component tag parser
 */
class ComponentTagParser extends AbstractTokenParser
{
    /**
     * Twig extension class  
     *
     * @var string|null
     */
    protected $twigExtensionClass;

    /**
     * Constructor
     *
     * @param string $twigExtensionClass
     */
    public function __construct(string $twigExtensionClass)
    {
        $this->twigExtensionClass = $twigExtensionClass;
    }

    /**
     * Parse tag 'component'
     *
     * @param Token $token
     * @return ComponentNode
     */
    public function parse(Token $token)
    {       
        $stream = $this->parser->getStream();
        // tag params
        $componentName = $stream->expect(Token::STRING_TYPE)->getValue();  
        // optinal type
        $type = ($stream->nextIf(Token::NAME_TYPE,'type') == true) ? $stream->expect(Token::STRING_TYPE)->getValue() : 'arikaim';         
        $stream->expect(Token::BLOCK_END_TYPE); 
        $body = $this->parser->subparse([$this,'decideTagEnd'],true);
        $stream->expect(Token::BLOCK_END_TYPE);
          
        return new ComponentNode(
            $body,
            $this->twigExtensionClass,
            [
                'name' => $componentName,
                'type' => $type
            ],
            $token->getLine(),
            $this->getTag()           
        );
    }

    /**
    * 
    * Return true when the expected end tag is reached.
    *
    * @param Token $token
    * @return bool
    */
    public function decideTagEnd(Token $token)
    {
        return $token->test('endcomponent');
    }

    /**
    * Tag name
    *
    * @return string
    */
    public function getTag()
    {
        return 'component';
    }
}
