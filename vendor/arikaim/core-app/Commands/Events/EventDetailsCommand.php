<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Events;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;

/**
 * Event details command
 */
class EventDetailsCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('event:details')->setDescription('Event Details');
        $this->addOptionalArgument('name','Event Name');
    }

    /**
     * Run command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {       
        global $arikaim;

        $this->showTitle();
     
        $name = $input->getArgument('name');
        if (empty($name) == true) {
            $this->showError('Event name required!');
            return;
        }
        
        $this->writeFieldLn('Name',$name);

        $event = $arikaim->get('event')->getEvents(['name' => $name]);
        if (\is_array($event) == false) {
            $this->showError('Not valid event name.');
            return;
        }

        $this->table()->setHeaders(['','']);
        $this->table()->setStyle('compact');

        $rows = [
            ['Id',$event[0]['uuid']],
            ['Title',$event[0]['title']],
            ['Description',$event[0]['description']],
            ['Extension',$event[0]['extension_name']],
            ['Status',ConsoleHelper::getStatusText($event[0]['status'])]
        ];
            
        $this->table()->setRows($rows);
        $this->table()->render();
        $this->newLine();
        $this->writeLn('Subscribers',' ','cyan');

        $subscribers = $arikaim->get('event')->getSubscribers($name); 

        foreach ($subscribers as $item) {
            $rows = [
                ['Handler', $item['handler_class']],
                ['Extension', $item['extension_name']],
                ['Status',ConsoleHelper::getStatusText($item['status'])]
            ];
            $this->table()->setRows($rows);
            $this->table()->render();
        }

        $this->showCompleted();
    }
}
