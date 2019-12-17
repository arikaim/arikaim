<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Utils;

use Arikaim\Core\Utils\Utils;

/**
 * Client Ip
 */
class ClientIp
{
    /**
     * Lookup in headers
     *
     * @var bool
     */
    protected static $lookInHeaders = [
        'Forwarded',
        'X-Forwarded-For',
        'X-Forwarded',
        'X-Cluster-Client-Ip',
        'Client-Ip',
    ];
 
    /**
     * Return client Ip address.
     *
     * @param object $request
     * @return string
     */
    public static function getClientIpAddress($request)
    {       
        $serverParams = $request->getServerParams();
        if (isset($serverParams['REMOTE_ADDR']) && Utils::isValidIp($serverParams['REMOTE_ADDR'])) {
            return $serverParams['REMOTE_ADDR'];     
        }
                               
        foreach (Self::$lookInHeaders as $header) {
            if ($request->hasHeader($header)) {
                $ip = Self::getFromHeader($request, $header);
                if (Utils::isValidIp($ip) == true) {
                    return $ip;                       
                }
            }
        }
      
        return null;
    }
    
    /**
     * Return header value
     *
     * @param object $request
     * @param string $header
     * @return mixed
     */
    public static function getFromHeader($request, $header)
    {
        $items = explode(',', $request->getHeaderLine($header));
        $value = trim(reset($items));
        if (ucfirst($header) == 'Forwarded') {
            foreach (explode(';', $value) as $part) {
                if (strtolower(substr($part, 0, 4)) == 'for=') {
                    $for = explode(']', $part);
                    $value = trim(substr(reset($for), 4), " \t\n\r\0\x0B" . "\"[]");
                    break;
                }
            }
        }
        
        return $value;
    }
}
