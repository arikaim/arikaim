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
    public static function getLabelText($text, $color = 'green')
    {
        return "<fg=$color>$text</>";
    }

    /**
     * Print CHECK MARK
     *
     * @return void
     */
    public static function checkMark()
    {
        return "<fg=green>\xE2\x9C\x93</>";
    }
    
    /**
     * Return status label text
     *
     * @param bool $status
     * @return string
     */
    public static function getStatusText($status)
    {
        return ($status == 1) ? "<fg=green>Enabled</>" : '<fg=red>Disabled</>';
    }

    /**
     * Return Yes/No text
     *
     * @param bool $value
     * @return string
     */
    public static function getYesNoText($value)
    {
        return ($value == true) ? "<fg=green>Yes</>" : '<fg=red>No</>';
    }

    /**
     * Return description text
     *
     * @param string $description
     * @return string
     */
    public static function getDescriptionText($description)
    {
        return "<fg=cyan>$description</>";
    }
}
