<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Content;

use Arikaim\Core\Interfaces\Content\ContentItemInterface;

/**
 * Content type action interface
 */
interface ActionInterface
{      
    const CONVERT_ACTION = 'convert';
    const EXPORT_ACTION  = 'export';
    const IMPORT_ACTION  = 'import';

    const ACTION_TYPES = [
        Self::CONVERT_ACTION,
        Self::EXPORT_ACTION,
        Self::IMPORT_ACTION
    ];

    /**
     * Init action
     *
     * @return void
     */
    public function init(): void;

    /**
     * Execute action
     *
     * @param ContentItemInterface $content    
     * @param array|null $options
     * @return mixed
     */
    public function execute($content, ?array $options = []); 

    /**
     * Get field name
     *
     * @return string
     */
    public function getName(): string;    

    /**
     * Get field title
     *
     * @return string|null
    */
    public function getTitle(): ?string;

    /**
     * Get field type
     *
     * @return string
     */
    public function getType(): string;
    
}
