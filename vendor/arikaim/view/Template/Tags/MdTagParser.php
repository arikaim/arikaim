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

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

use Arikaim\Core\View\Template\Tags\MdNode;

/**
 * Markdown tag parser
 */
class MdTagParser extends AbstractTokenParser
{
    /**
     * Parse tag 'md'
     *
     * @param Token $token
     * @return AccessNode
     */
    public function parse(Token $token)
    {
        $line = $token->getLine();
        $stream = $this->parser->getStream();
        // tag params
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this,'decideTagEnd'],true);
        $stream->expect(Token::BLOCK_END_TYPE);
        
        return new MdNode($body,[],$line,$this->getTag());
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
        return $token->test('endmd');
    }

    /**
    * Tag name
    *
    * @return string
    */
    public function getTag()
    {
        return 'md';
    }
}
