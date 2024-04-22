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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Helper\ProgressBar;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;

use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\Utils\Utils;

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
    protected $default = false;
    
    /**
     * Table output
     *
     * @var Symfony\Component\Console\Helper\Table
     */
    protected $table = null;

    /**
     * Event dispatcher
     *
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * Output type
     *
     * @var string|null
     */
    protected $outputType = null;

    /**
     * Command result data
     *
     * @var array
     */
    protected $result = [];

    /**
     * Progress bar ref
     *
     * @var ProgressBar|null
     */
    private $progress;

    /**
     * Input
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * Output
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * Constructor
     *
     * @param string|null $name
     * @param string|null $description
     */
    public function __construct(?string $name = null, ?string $description = null) 
    {
        parent::__construct($name);
        
        $this->result = [];
        $this->default = false;
        $this->progress = null;

        if (empty($name) == false) {
            $this->setName($name);
        }
        if (empty($description) == false) {
            $this->setDescription($description);
        }
    }

    /**
     * Get progress bar
     *
     * @param integer|null $max
     * @return ProgressBar
     */
    protected function progress(?int $max = null)
    {
        if ($max != null) {
            $this->progress->setMaxSteps($max);
        }
        
        return $this->progress;
    }

    /**
     * Set output type
     *
     * @param string|null $outputType
     * @return void
     */
    public function setOutputType(?string $outputType): void
    {
        $this->outputType = $outputType;
    }

    /**
     * Get execution result
     *
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * Return true if output is to console
     *
     * @return boolean
     */
    public function isConsoleOutput(): bool
    {
        return empty($this->outputType);
    }

    /**
     * Return true if output is to console
     *
     * @return boolean
     */
    public function isJsonOutput(): bool
    {
        return ($this->outputType == 'json');
    }

    /**
     * Set event dispatcher
     *
     * @param object $dispatcher
     * @return void
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
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
     * @return mixed
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->style = new SymfonyStyle($input,$output);
        $this->table = new Table($output);
        $this->progress = new ProgressBar($output,100);

        $this->addOptionalOption('output','Output format',false);
        $beforeEvent = new ConsoleCommandEvent($this,$input,$output);      
        $this->dispatcher->dispatch($beforeEvent,'before.execute.commmand');

        $exitCode = parent::run($input,$output);
        $this->result['status'] = ($exitCode == 0) ? 'ok' : 'error';
        
        // command executed
        $afterEvent = new ConsoleCommandEvent($this,$input,$output);
        $this->dispatcher->dispatch($afterEvent,'after.execute.commmand');

        return $exitCode;
    }
 
    /**
     * Add required argument 
     *
     * @param string $name
     * @param string $description
     * @param mixed|null $default
     * @return void
     */
    public function addRequiredArgument(string $name, string $description = '', $default = null): void
    {
        $this->addArgument($name,InputArgument::REQUIRED,$description,$default);
    }

    /**
     * Add optional argument
     *
     * @param string $name
     * @param string $description
     * @param mixed|null $default
     * @return void
     */
    public function addOptionalArgument(string $name, string $description = '', $default = null): void
    {
        $this->addArgument($name,InputArgument::OPTIONAL,$description,$default);
    }

    /**
     * Add optional option
     *
     * @param string $name
     * @param string $description
     * @param mixed|null $default
     * @return void
     */
    public function addOptionalOption(string $name, string $description = '', $default = null): void
    {
        $this->addOption($name,null,InputOption::VALUE_OPTIONAL,$description,$default);
    }

    /**
     * Run console command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {      
        $this->outputType = $input->getOption('output');

        $result = $this->executeCommand($input,$output);
        
        return (empty($result) == true) ? 0 : $result;
    }

    /**
     * Get table obj
     *
     * @return Symfony\Component\Console\Helper\Table
     */
    public function table()
    {
        return $this->table;
    } 

    /**
     * Set command as default
     *
     * @param boolean $default
     * @return void
     */
    public function setDefault(bool $default = true): void
    {
        $this->default = $default;
    }

    /**
     * Return true if command is default.
     *
     * @return boolean
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * Show command title
     *
     * @param string?null $title
     * @param string
     * @return void
     */
    public function showTitle(?string $title = null,string $space = ' '): void
    {
        $title = $title ?? $this->getDescription();
        if ($this->isJsonOutput() == true) {
            $this->result['title'] = $title;
            return;
        }
        
        $title = ConsoleHelper::getDescriptionText($title);
        
        $this->style->newLine();
        $this->style->writeLn($space . $title);
        $this->style->newLine();
    }

    /**
     * Show error message
     *
     * @param string $message
     * @param string $label
     * @param string $space
     * @return void
     */
    public function showError(string $message, string $label = 'Error:', string $space = ' '): void
    {
        if ($this->isJsonOutput() == true) {
            $this->result['error'] = $message;
            return;
        }

        $this->style->newLine();
        $this->style->writeLn($space . '<error>' . $label  . ' ' . $message . '</error>');
        $this->style->newLine();
    }

    /**
     * Show multipel errors
     *
     * @param string|array $errors
     * @param string $label
     * @param string $space
     * @return void
     */
    public function showErrors($errors, string $label = 'Error:', string $space = ' '): void
    {
        if (\is_array($errors) == true) {
            foreach($errors as $error) {
                $this->showError($error,$label,$space);
            }
            return;
        }

        $this->showError($errors,$label,$space);
    }

    /**
     * Show error details
     *
     * @param string|array $details
     * @param string $space
     * @return void
     */
    public function showErrorDetails($details, $space = ' '): void
    {
        if ($this->isJsonOutput() == true) {
            return;
        }

        if (\is_array($details) == true) {
            foreach ($details as $item) {
                $this->style->writeLn($space . ConsoleHelper::errorMark() . ' ' . $item);
            }
            return;
        }

        $this->style->writeLn($space . ConsoleHelper::errorMark() . ' ' . $details);
    } 

    /**
     * Show CHECK MARK
     *
     * @param string $space
     * @return void
     */
    public function checkMark(string $space = ' '): void
    {
        $this->style->write(ConsoleHelper::checkMark($space));
    }

    /**
     * New line
     *
     * @return void
     */
    public function newLine(): void
    {
        if ($this->isJsonOutput() == true) {
            return;
        }

        $this->style->newLine();
    }

    /**
     * Show 'done' message
     *
     * @param string|null $label
     * @param string $space
     * @return void
     */
    public function showCompleted(?string $label = null,string $space = ' '): void
    {
        if ($this->isJsonOutput() == true) {
            return;
        }

        $label = (empty($label) == true) ? 'done.' : $label;           
        $this->style->newLine();
        $this->style->writeLn($space . '<fg=green>' . $label . '</>');
        $this->style->newLine();
    }

    /**
     * Write field
     *
     * @param string $label
     * @param mixed $value
     * @param string $color
     * @param string $space
     * @return void
     */
    public function writeField(string $label, $value,string $color = 'cyan',string $space = ' '): void
    {
        if ($this->isJsonOutput() == true) {
            $key = Utils::slug($label,'_');
            $this->result[$key] = $value;
            return;
        }

        $this->style->write($space);
        $label = ConsoleHelper::getLabelText($label,$color);
        $this->style->write($label . ' ');
        $this->style->write($value);
    }

    /**
     * Write field
     *
     * @param string $label
     * @param mixed $value
     * @param string $color
     * @param string $space
     * @return void
     */
    public function writeFieldLn(string $label, $value,string $color = 'cyan',string $space = ' '): void
    {
        $this->writeField($label,$value,$color,$space);
        if ($this->isJsonOutput() == true) {
            return;
        }

        $this->newLine();
    }

    /**
     * Write line
     *
     * @param string $text
     * @param string $space
     * @param string? $color
     * @return void
     */
    public function writeLn(string $text, string $space = ' ', ?string $color = null): void
    {
        if ($this->isJsonOutput() == true) {
            return;
        }

        if (empty($color) == false) {
            $text = ConsoleHelper::getLabelText($text,$color);
        }
      
        $this->style->writeLn($space . $text);
    }

    /**
     * Ask console question
     *
     * @param string $text
     * @param mixed $default
     * @param array $autocomplete
     * @return mixed
     */
    protected function question(string $text, $default = null, array $autocomplete = [])
    {
        $question = new Question($text,$default);
        $helper = $this->getHelper('question');
        $question->setAutocompleterValues($autocomplete);

        return $helper->ask($this->input,$this->output,$question);
    }

    /**
     * Ask confirmation question
     *
     * @param string  $text
     * @param boolean $default
     * @return bool
     */
    protected function confirmation(string $text, bool $default = true): bool
    {
        $question = new ConfirmationQuestion($text,$default,'/^(y|j)/i');
        $helper = $this->getHelper('question');

        return $helper->ask($this->input,$this->output,$question);
    }

    /**
     * Choice question
     *
     * @param string      $text
     * @param array       $items
     * @param boolean     $multiselect
     * @param string|null $error
     * @return mixed
     */
    protected function choice(
        string $text, 
        array $items, 
        bool $multiselect = false, 
        ?string $error = null
    )
    {
        $question = new ChoiceQuestion($text,$items);
        $question->setMultiselect($multiselect);
        if (empty($error) == false) {
            $question->setErrorMessage($error);
        }

        $question->setAutocompleterValues($items);
        $helper = $this->getHelper('question');

        return $helper->ask($this->input,$this->output,$question);
    }

}
