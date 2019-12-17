<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Logger;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\AbstractHandler;
use Monolog\Formatter\FormatterInterface;

use Arikaim\Core\Utils\File;
use Arikaim\Core\Logger\JsonLogsFormatter;
use Arikaim\Core\Logger\LogsProcessor;

/**
 * Logger
 */
class Logger
{
    /**
     * Logger object
     *
     * @var Monolog\Logger
     */
    protected $logger;
    
    /**
     * Enable/Disable logger
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Logs file name
     *
     * @var string
     */
    private $fileName;

    /**
     * Logs directory
     *
     * @var string
     */
    private $logsDir;

    /**
     * Logs Formatter
     *
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * Logs handler
     *
     * @var AbstractHandler
     */
    private $handler;

    /**
     * Constructor
     *
     * @param string $logsDir
     * @param AbstractHandler $handler
     * @param FormatterInterface $formatter
     * @param string $fileName
     */
    public function __construct($logsDir, AbstractHandler $handler = null, FormatterInterface $formatter = null, $fileName = null) 
    {         
        $this->fileName = (empty($fileName) == true) ? "errors.log" : $fileName;
        $this->logsDir = $logsDir;      
        $this->enabled = true;
        $this->handler = (empty($handler) == true) ? new StreamHandler($this->getLogsFileName(), MonologLogger::DEBUG) : $handler;
        $this->formatter = (empty($formatter) == true) ? new JsonLogsFormatter() : $formatter;
        $this->handler->setFormatter($this->formatter); 

        $this->logger = new MonologLogger('system');                
        $this->logger->pushHandler($this->handler);
        $this->logger->pushProcessor(new LogsProcessor());         
    }

    /**
     * Disable logger
     *
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Get formatter
     *
     * @return FormatterInterface
     */
    public function getFrmatter()
    {
        return $this->formatter;
    }

    /**
     * Get handler
     *
     * @return AbstractHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Get logs file name
     *
     * @return string
     */
    public function getLogsFileName()
    {
        return $this->logsDir . $this->fileName;
    }

    /**
     * Delete logs file
     *
     * @return bool
     */
    public function deleteSystemLogs()
    {
        return (File::exists($this->getLogsFileName()) == false) ? true : File::delete($this->getLogsFileName());
    }

    /**
     * Read logs file with paginator
     *
     * @return void
     */
    public function readSystemLogs()
    {       
        $text ="[" . File::read($this->getLogsFileName());      
        $text = rtrim($text,",\n");
        $text .="]\n";

        $logs = json_decode($text,true);
      
        return $logs;
    }

    /**
     * Call logger function
     *
     * @param string $name
     * @param mixed $arguments
     * @return boolean
     */
    public function __call($name, $arguments)
    {           
        $message = $arguments[0];
        $context = isset($arguments[1]) ? $arguments[1] : [];

        return ($this->enabled == true) ? $this->logger->{$name}($message,$context) : false;          
    }
    
    /**
     * Add log record
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function log($level, $message, array $context = [])
    {   
        return ($this->enabled == true) ? $this->logger->log($level,$message,$context) : false;        
    } 

    /**
     * Add error log
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function error($message,$context = [])
    {      
        return ($this->enabled == true) ? $this->logger->error($message,$context) : false;      
    }

    /**
     * Return stats logger 
     *
     * @return Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set logger
     *
     * @param Monolog\Logger $logger
     * @return void
     */
    public function setLogger($logger)
    {
        return $this->logger = $logger;
    }
}
