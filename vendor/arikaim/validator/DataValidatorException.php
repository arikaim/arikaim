<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator;

use Exception;
use Throwable;

/**
 * Data validator exception class
 */
class DataValidatorException extends Exception
{
    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * Errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor
     * 
     * @param string          $message
     * @param array           $errors
     * @param int             $code
     * @param Throwable|null  $previous
     */
    public function __construct(       
        array $errors,
        string $message = '', 
        int $code = 0, 
        ?Throwable $previous = null
    ) 
    {
        parent::__construct($message,$code,$previous);  
        $this->errors = $errors;   
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
