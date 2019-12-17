<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Session;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Http\Session;

/**
 * Session info
 * 
 */
class InfoCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name cache:clear 
     * @return void
     */
    protected function configure()
    {
        $this->setName('session:info')->setDescription('Session info');
    }

    /**
     * Command code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function executeCommand($input, $output)
    {
        $this->showTitle('Session info.');
        
        $params = Session::getParams();

        $label = ($params['use_cookies'] == true) ? 'true' : 'false';

        $this->style->writeLn('Id: ' . Session::getId());
        $this->style->writeLn('Use cookies: ' . $label);
        $this->style->writeLn('Save Path: ' . ini_get( 'session.save_path'));      
        $this->style->writeLn('Lifetime: ' . Session::getLifetime());

        $this->showCompleted();
    }
}
