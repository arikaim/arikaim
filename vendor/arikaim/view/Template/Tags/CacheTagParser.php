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

use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

use Arikaim\Core\View\Template\Tags\CacheNode;

/**
 * Cache parser class
 */
class CacheTagParser extends AbstractTokenParser
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
     * Parse
     *
     * @param Token $token
     * @return Node
     */
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();     
        $key = $stream->expect(Token::STRING_TYPE)->getValue();
        $keyName = ($stream->nextIf(Token::NAME_TYPE,'keyName') == true) ? $stream->expect(Token::STRING_TYPE)->getValue() : '';
   
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this,'decideTagEnd'],true);
        $stream->expect(Token::BLOCK_END_TYPE);
        
        return new CacheNode(
            $key,
            $keyName,
            null,
            $body,
            $token->getLine(),
            $this->getTag(),
            $this->twigExtensionClass
        );
    }

    /**
     * End tag name
     *
     * @param Token $token
     * @return boolean
     */
    public function decideTagEnd(Token $token): bool
    {
        return $token->test('endcache');
    }

    /**
     * Tag name
     *
     * @return string
     */
    public function getTag(): string
    {
        return 'cache';
    }
}
