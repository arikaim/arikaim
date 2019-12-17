<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System\Error\Renderer;

use Symfony\Component\Console\Output\ConsoleOutput;
use Arikaim\Core\System\Error\ErrorRendererInterface;

/**
 * Render error
 */
class ConsoleErrorRenderer implements ErrorRendererInterface
{
    /**
     * Render error
     *
     * @param array $errorDetails
     * @return void
     */
    public function render($errorDetails)
    {
        $output = new ConsoleOutput();

        $output->writeln('');
        $output->writeln('Application error');
        $output->writeln('Message:  ' . $errorDetails['message']);
        $output->writeln('File:  ' . $errorDetails['file']);
        $output->writeln('Type:  ' . $errorDetails['type_text']);      
        $output->writeln('Line:  ' . $errorDetails['line']);
        $output->writeln('Trace:  ' . $errorDetails['trace_text']);
    }
}
