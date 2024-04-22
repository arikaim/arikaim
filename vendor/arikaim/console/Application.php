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

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\ConsoleEvents;

use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Console\ShellCommand;

/**
 * Console application
 */
class Application
{       
    const LOG_MESSAGE           = 'Command executed.';
    const LOG_ERROR_MESSAGE     = 'Error command execution.';
    const LOG_TERMINATE_MESSAGE = 'Console command terminated.';

    /**
     * App object
     *
     * @var Symfony\Component\Console\Application
     */
    protected $application;

    /**
     * Console app title
     *
     * @var string
     */
    protected $title;

    /**
     * App version
     *
     * @var string
     */
    protected $version;

    /**
     * Event dispatcher
     *
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * Logger
     *
     * @var Arikaim\Core\Interfaces\LoggerInterface|null
     */
    protected $logger = null;

    /**
     * Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Constructor
     *
     * @param string $title
     * @param string $version
     * @param Arikaim\Core\Interfaces\LoggerInterface|null $logger
     */
    public function __construct(string $title, string $version = '', array $options = [], $logger = null) 
    {
        $this->title = $title;
        $this->version = $version;
        $this->options = $options;
        $this->logger = $logger;

        $this->application = new ConsoleApplication("\n " . $title,$version);    
    
        // add shell command 
        $shell = new ShellCommand('shell',$title);
        $this->application->add($shell);
        if ($shell->isDefault() == true) {
            $this->application->setDefaultCommand($shell->getName());
        }
        // events
        $this->dispatcher = new EventDispatcher();

        $this->dispatcher->addListener('before.execute.commmand', function(ConsoleCommandEvent $event) {
            // gets the command to be executed          
            $outputType = $event->getInput()->getOption('output');  
            $event->getCommand()->setOutputType($outputType);
        });

        $this->dispatcher->addListener('after.execute.commmand', function(ConsoleCommandEvent $event) {  
            $command = $event->getCommand();
            $name = (\is_object($command) == true) ? $command->getName() : null;
              
            if ($command->isJsonOutput() == true) {
                echo Utils::jsonEncode($command->getResult(),true);
            }
            
            $this->log(Self::LOG_MESSAGE,['command' => $name]);
        });

        // errors event
        $this->dispatcher->addListener(ConsoleEvents::ERROR, function(ConsoleErrorEvent $event) {                             
            $name = (\is_object($event->getCommand()) == true) ? $event->getCommand()->getName() : null;

            $this->logError(Self::LOG_ERROR_MESSAGE,[                      
                'command'   => $name,
                'error'     => $event->getError(),
                'exit_code' => $event->getExitCode()
            ]);
        });

        // terminate event
        $this->dispatcher->addListener(ConsoleEvents::TERMINATE, function(ConsoleTerminateEvent $event) {              
            if ($event->getExitCode() == 0) {
                return;
            }                 
            $name = (\is_object($event->getCommand()) == true) ? $event->getCommand()->getName() : null;

            $this->logError(Self::LOG_TERMINATE_MESSAGE,[
                'command'   => $name,               
                'exit_code' => $event->getExitCode()
            ]);
        });

        $this->application->setDispatcher($this->dispatcher);
    }

    /**
     * Log message
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    protected function log(string $message, array $context = []): bool
    {      
        if (($this->options['log'] ?? false) == false) {
            return false;
        }

        return ($this->logger === null) ? false : $this->logger->info($message,$context);
    }

     /**
     * Log error message
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    protected function logError(string $message, array $context = []): bool
    {      
        if (($this->options['logErrors'] ?? false) == false) {
            return false;
        }

        return ($this->logger === null) ? false : $this->logger->error($message,$context);
    }

    /**
     * Get event dispatcher
     *
     * @return EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Run console cli
     *
     * @return void
     */
    public function run(): void
    {
        $this->application->run();
    }

    /**
     * Add commands to console app
     *
     * @param array $commands
     * @return void
     */
    public function addCommands(array $commands): void
    {
        foreach ($commands as $class) {          
            $command = Factory::createInstance($class);
          
            if ($command != null) {
                $command->setDispatcher($this->dispatcher);
                $this->application->add($command);
                if ($command->isDefault() == true) {
                    $this->application->setDefaultCommand($command->getName());
                }
            }
        }     
    }
}
