<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Console;

/**
 * Console helper class
 */
class ConsoleHelper
{  
    /**
     * Return console label text
     *
     * @param string $text
     * @param string $color
     * @return string
     */
    public static function getLabelText(string $text,string $color = 'green'): string
    {
        return '<fg=' . $color . '>' . $text . '</>';
    }

    /**
     * Get CHECK MARK
     *
     * @param string $space
     * @return string
     */
    public static function checkMark(string $space = ' '): string
    {
        return $space . "<fg=green>\xE2\x9C\x93</>" . $space;
    }

    /**
     * Get error mark
     *
     * @param string $space
     * @return string
     */
    public static function errorMark(string $space = ' '): string
    {
        return $space . '<fg=red>x</>' . $space;
    }

    /**
     * Get warning
     *
     * @return string
     */
    public static function warning(string $label = '!'): string
    {
        return '<fg=yellow>' . $label . '</>';
    }

    /**
     * Return status label text
     *
     * @param int $status
     * @return string
     */
    public static function getStatusText(int $status): string
    {
        return ($status == 1) ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>';
    }

    /**
     * Return Yes/No text
     *
     * @param bool $value
     * @return string
     */
    public static function getYesNoText(bool $value): string
    {
        return ($value == true) ? '<fg=green>Yes</>' : '<fg=red>No</>';
    }

    /**
     * Return description text
     *
     * @param string $description
     * @return string
     */
    public static function getDescriptionText(string $description): string
    {
        return '<fg=cyan>' . $description . '</>';
    }
}
