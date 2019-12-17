<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Extensions\Category\Console;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Collection\Arrays;

/**
 * Delete categories command
 */
class CategoryDelete extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('category:delete')->setDescription('Delete categories.'); 
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
        $this->style->writeLn('');
        $category = Model::Category('category')->all();
        $relations = Model::CategoryRelations('category');

        $this->style->writeLn('Total categories: ' . $category->count());
        $this->style->writeLn('');

        $deleted = 0;
        foreach ($category as $item) {
            $this->style->writeLn('');
            $this->style->writeLn('Category: ' . Arrays::toString($item->getTitle()) );    
            $count = $item->translations()->count();
            $rows = $relations->getRows($item->id);

            if ($count == 0) {
                $this->style->writeLn('delete ...');
                $item->remove($item->id);
                $deleted++;
            }
            
            if ($rows->count() == 0) {               
                $this->style->writeLn('No relations delete category');
                $item->remove($item->id);
                $deleted++;
            }
        }
        $this->style->writeLn('Deleted categories: ' . $deleted);
        $this->style->writeLn('');
        
        $this->showCompleted();
    }
}
