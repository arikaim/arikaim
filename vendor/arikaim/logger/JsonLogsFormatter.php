<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Logger;

use Monolog\Formatter\FormatterInterface;

use Arikaim\Core\Utils\Utils;

/**
 * Json formatter implementation
 */
class JsonLogsFormatter implements FormatterInterface
{
    /**
     * Format log record
     *
     * @param array $record
     * @return string
     */
    public function format(array $record)
    {
        return Utils::jsonEncode($record) . ",\n";
    }
    
    /**
     * Format multiple log records
     *
     * @param array $records
     * @return array
     */
    public function formatBatch(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }
        
        return $records;
    }
}
