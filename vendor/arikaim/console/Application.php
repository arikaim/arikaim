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

use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Console\ShellCommand;

/**
 * Console application
 */
class Application
{       
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
     * Constructor
     */
    public function __construct($title, $version = '') 
    {
        $this->title = $title;
        $this->version = $version;
        $this->application = new ConsoleApplication("\n $title",$version);    

        // add shell command 
        $shell = new ShellCommand('shell',$title);
        $this->application->add($shell);
        if ($shell->isDefault() == true) {
            $this->application->setDefaultCommand($shell->getName());
        }
    }

    /**
     * Run console cli
     *
     * @return void
     */
    public function run()
    {
        $this->application->run();
    }

    /**
     * Add commands to console app
     *
     * @param array $commands
     * @return void
     */
    public function addCommands($commands)
    {
        if (is_array($commands) == false) {
            return false;
        }

        foreach ($commands as $class) {          
            $command = Factory::createInstance($class);
            if (is_object($command) == true) {
                $this->application->add($command);
                if ($command->isDefault() == true) {
                    $this->application->setDefaultCommand($command->getName());
                }
            }
        }
    }
}
