<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/

use \Arikaim\Core\Utils\Utils;

/**
 * Create event obj
 *
 * @param array $params Event params
 * @return Arikaim\Core\Events\Event 
 */
function createEvent(array $params = [])
{
    return new \Arikaim\Core\Events\Event($params);
}

/**
 * Add profile pin
 *
 * @param string $label
 * @return void
 */
function addProfilerPin(string $label): void
{   
    $GLOBALS['profilePins'][] = [
        'label' => $label,
        'time'  => Utils::getExecutionTime()
    ];
}

/**
 * Get profiler data
 *
 * @return array
 */
function getProfilerData(): array
{
    return $GLOBALS['profilePins'] ?? [];
} 