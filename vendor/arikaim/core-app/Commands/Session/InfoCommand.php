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
    protected function configure(): void
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
        $this->showTitle();
    
        $params = Session::getParams();
        $label = ($params['use_cookies'] == true) ? 'true' : 'false';

        $this->writeFieldLn('Id',Session::getId());
        $this->writeFieldLn('Use cookies',$label);
        $this->writeFieldLn('Save Path',\ini_get('session.save_path'));
        $this->writeFieldLn('Lifetime',Session::getLifetime());

        $this->showCompleted();
    }
}
