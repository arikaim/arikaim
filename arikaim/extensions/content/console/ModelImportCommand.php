<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Extensions\Content\Console;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Actions\Actions;

/**
 * Model import console command
 */
class ModelImportCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('model:import');
        $this->setDescription('Import model from json file.'); 
        $this->addOptionalArgument('file-name','File name');
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {       
        $this->showTitle();

        $fileName = $input->getArgument('file-name');
        if (empty($fileName) == true) {
            $fileName = $this->question("Enter file name: ",null);            
        }

        $action = Actions::create('ModelImport','content',[
            'file_name' => $fileName
        ])->getAction();

        $result = $action->run();

        if ($result === false) {
            $this->showError($action->getError());           
        } else {
            $this->writeLn($action->get('message'));
            $this->showCompleted();
        }
    }   
}
