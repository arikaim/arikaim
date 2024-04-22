<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Image\Classes;

/**
 * Color helpers
 */
class Color 
{
    const BLACK = [0,0,0];
    const WHITE = [255,255,255];

    /**
     * Convert hex color to RGB
     *
     * @param string|null $color
     * @param array $default
     * @return array
     */
    public static function hexToRgb(?string $color, array $default = Self::BLACK): array
    {
        if (empty($color) == true) {
            return $default;
        }

        return \array_map(
            function ($c) {
              return \hexdec(\str_pad($c, 2, $c));
            },
            \str_split(\ltrim($color,'#'),\strlen($color) > 4 ? 2 : 1)
        );
    }
}
