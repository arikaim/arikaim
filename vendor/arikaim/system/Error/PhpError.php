<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System\Error;

use Arikaim\Core\Utils\Utils;

/**
 * Php error base class
 */
class PhpError
{
    /**
     * Convert error to array
     *
     * @param \Throwable|array $error
     * @return array
     */
    public static function toArray($error): array
    {
        if (\is_array($error) == true) {
            $errorDetails = [
                'code'          => $error['type'],
                'class'         => '',
                'base_class'    => '',
                'type_text'     => Self::getErrorTypeText($error['type']),     
                'trace_text'    => ''               
            ];

            return \array_merge($error,$errorDetails);
        } 

        return [
            'line'          => $error->getLine(),
            'code'          => $error->getCode(),
            'class'         => \get_class($error),
            'base_class'    => Utils::getBaseClassName(get_class($error)),
            'type_text'     => Self::getErrorTypeText($error->getCode()),
            'file'          => $error->getFile(),
            'trace_text'    => $error->getTraceAsString(),
            'message'       => $error->getMessage()
        ];
    }

    /**
     * Convert to string
     *
     * @param array $errorDetails
     * @return string
     */
    public static function toString(array $errorDetails): string
    {
        $message = $errorDetails['message'] ?? '';
        $message .= (empty($errorDetails['class'] ?? null) == false) ? ' class:' . $errorDetails['class'] : '';     
        $message .= (empty($errorDetails['line'] ?? null) == false) ? ' line: ' . $errorDetails['line'] : '';
        $message .= (empty($errorDetails['file'] ?? null) == false) ? ' file: ' . $errorDetails['file'] : '';

        return $message;
    }

    /**
     * Get JSON error message
     *
     * @return string
     */
    public static function getJsonError(): string
    {
        switch (\json_last_error()) {
            case JSON_ERROR_NONE:
                $error = null;
                break;
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $error = 'Unknown error';
                break;
        }

        return $error;
    }

    /**
     * Get error type
     *
     * @param integer $type
     * @return string
     */
    public static function getErrorTypeText($type): string
    {
        $errorTypeText = [
            E_ERROR      => 'Fatal run-time error',
            E_WARNING    => 'Warning',
            E_PARSE      => 'Compile-time parse error',
            E_NOTICE     => 'Notices',
            E_CORE_ERROR => 'Fatal error'
        ];

        return $errorTypeText[$type] ?? 'Run-time error';
    }

    /**
     * Get posix error
     *
     * @return mixed
     */
    public static function getPosixError()
    {
        $err = \posix_get_last_error();
        
        return ($err > 0) ? \posix_strerror($err) : '';
    }
}
