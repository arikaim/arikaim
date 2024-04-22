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

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Arikaim\Core\Collection\PropertiesFactory;
use Arikaim\Core\Collection\Interfaces\PropertiesInterface;

/**
 * Config properties console helper
 */
class ConfigProperties
{       
    /**
     * Create console table 
     *
     * @param PropertiesInterface|array $properties
     * @param OutputInterface $output
     * @return Table
     */
    public static function createPropertiesTable($properties, $output): Table
    {
        if (\is_array($properties) == true) {
            $properties = PropertiesFactory::createFromArray($properties);
        }
        $table = new Table($output);
        $table->setHeaders(['Key','Title','Value','Default']);
        $table->setStyle('compact');
     
        foreach($properties as $item) {
            $row = [
                $item['name'],
                $item['title'],
                $item['value'],
                $item['default']
            ];
            $table->addRow($row);            
        }

        return $table;
    }
}
