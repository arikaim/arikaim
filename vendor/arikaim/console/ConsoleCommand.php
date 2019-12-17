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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Base class for all commands
 */
abstract class ConsoleCommand extends Command
{       
    /**
     * Style obj reference
     *
     * @var SymfonyStyle
     */
    protected $style;

    /**
     * Set to true for default command
     *
     * @var bool
     */
    protected $default;
    
    /**
     * Constructor
     *
     * @param string|null $name
     * @param string|null $description
     */
    public function __construct($name = null, $description = null) 
    {
        parent::__construct($name);
        
        if (empty($name) == false) {
            $this->setName($name);
        }
        if (empty($description) == false) {
            $this->setDescription($description);
        }
    }

    /**
     * Abstract method.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract protected function executeCommand($input, $output);

    /**
     * Run method wrapper
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->style = new SymfonyStyle($input, $output);

        return parent::run($input, $output);
    }

    /**
     * Add required argument 
     *
     * @param string $name
     * @param string $description
     * @param bool $default
     * @return void
     */
    public function addRequiredArgument($name, $description = '', $default = null)
    {
        $this->addArgument($name,InputArgument::REQUIRED,$description,$default);
    }

    /**
     * Add optional argument
     *
     * @param string $name
     * @param string $description
     * @param bool $default
     * @return void
     */
    public function addOptionalArgument($name, $description = '', $default = null)
    {
        $this->addArgument($name,InputArgument::OPTIONAL,$description,$default);
    }

    /**
     * Run console command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->executeCommand($input,$output);
    }

    /**
     * Set command as default
     *
     * @param boolean $default
     * @return void
     */
    public function setDefault($default = true)
    {
        $this->default = $default;
    }

    /**
     * Return true if command is default.
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * Show command title
     *
     * @param string $title
     * @return void
     */
    public function showTitle($title)
    {
        $this->style->newLine();
        $this->style->writeLn($title);
        $this->style->newLine();
    }

    /**
     * Show error message
     *
     * @param string $message
     * @param string $label
     * @return void
     */
    public function showError($message, $label = "Error:")
    {
        $this->style->newLine();
        $this->style->writeLn("<error>$label $message</error>");
        $this->style->newLine();
    }

    /**
     * Show 'done' message
     *
     * @param string $label
     * @return void
     */
    public function showCompleted($label = null)
    {
        $label = ($label == null) ? 'done.' : $label;           
        $this->style->newLine();
        $this->style->writeLn("<fg=green>$label</>");
    }
}
