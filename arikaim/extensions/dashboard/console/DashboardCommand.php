<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2018 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
 */
namespace Arikaim\Extensions\Dashboard\Console;

use Arikaim\Core\System\Console\ConsoleCommand;

class DashboardCommand extends ConsoleCommand
{  
    protected function configure()
    {
        $this->setName('dashboard')->setDescription('Arikaim Dashboard Extension');
        
    }

    protected function executeCommand($input, $output)
    {
        $this->style->text('Dashboard Extension');
    }
}
