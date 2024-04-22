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

use Arikaim\Core\View\Template\Tags\ErrorNode;

/**
 * Error tag parser
 */
class ErrorTagParser extends AbstractTokenParser
{
    /**
     * Parse tag 'error'
     *
     * @param Token $token
     * @return ErrorNode
     */
    public function parse(Token $token)
    {
        $line = $token->getLine();
        $stream = $this->parser->getStream();
        // tag params
        $nodes = [];
        if ($stream->test(Token::STRING_TYPE)) {
            $nodes['message'] = $this->parser->getExpressionParser()->parseExpression();
            $nodes['message'] = $nodes['message'] ?? '';
        }
        $stream->expect(Token::BLOCK_END_TYPE);
       
        return new ErrorNode($nodes,[],$line,$this->getTag());
    }

    /**
    * Tag name
    *
    * @return string
    */
    public function getTag()
    {
        return 'error';
    }
}
