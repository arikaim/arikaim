<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Actions
*/
namespace Arikaim\Core\Actions;

use Arikaim\Core\Actions\Action;

/**
 * Action not found
 */
class ActionNotFound extends Action
{
    /**
     * Init action
     *
     * @return void
     */
    public function init(): void
    {        
        $this->error('Action ' . $this->getOption('name') . ' not found');
    }

    /**
     * Run action
     *
     * @param mixed $params
     * @return mixed
     */
    public function run(...$params)
    {
    }
}
