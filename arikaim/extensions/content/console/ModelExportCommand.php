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
 * Model export console command
 */
class ModelExportCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('model:export');
        $this->setDescription('Export model to json file.'); 
        $this->addOptionalArgument('model-class','Model class');
        $this->addOptionalArgument('schema-class','Model class');
        $this->addOptionalArgument('extension','Model extension');
        $this->addOptionalArgument('uuid','Model Uuid');
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
        
        $modelClass = $input->getArgument('model-class');
        if (empty($modelClass) == true) {
            $modelClass = $this->question("Enter model class: ",null);           
        }

        $schemaClass = $input->getArgument('schema-class');
        if (empty($schemaClass) == true) {
            $schemaClass = $this->question("Enter schema class: ",null);           
        }

        $extension = $input->getArgument('extension');
        if (empty($extension) == true) {
            $extension = $this->question("Enter model extension: ",null);            
        }

        $uuid = $input->getArgument('uuid');
        if (empty($uuid) == true) {
            $uuid = $this->question("Enter model Uuid: ",null);            
        }

        $fileName = $input->getArgument('file-name');
        if (empty($fileName) == true) {
            $fileName = $this->question("Enter file name: ",null);            
        }

        $action = Actions::create('ModelExport','content',[
            'model_class'   => $modelClass,
            'schema_class'  => $schemaClass,
            'extension'     => $extension,
            'uuid'          => $uuid,
            'file_name'     => $fileName
        ])->getAction();

        $result = $action->run();

        if ($result !== false) {
            $this->writeLn('Model exported to file: ');
            $this->writeLn('../storage/' . $action->get('file_name'));
            $this->showCompleted();
        } else {
            $this->showError($action->getError());
        }
    }   
}
