<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Framework;

use Exception;
use Throwable;
use Arikaim\Core\Http\HttpStatusCode;

/**
 * Http exception class
 */
class HttpException extends Exception
{

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $httpStatusCode;

    /**
     * Constructor
     * 
     * @param int               $httpResponseCode
     * @param int               $code
     * @param Throwable|null    $previous
     */
    public function __construct(int $httpStatusCode, int $code = 0, ?Throwable $previous = null) 
    {
        $this->httpStatusCode = $httpStatusCode;
        $message = HttpStatusCode::getMessage($httpStatusCode);
        parent::__construct($message,$code,$previous);  
    }

    /**
     * Get http status code
     *
     * @return integer
     */
    public function getStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
