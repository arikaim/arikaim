<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Logger\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Models\Logs;

/**
 * Db handler for monolog
 */
class DbHandler extends AbstractProcessingHandler
{
    /**
     * Db model
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $dbModel;

    /**
     * Constructor
     *
     * @param mixed $level
     * @param boolean $bubble
     */
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        $this->dbModel = new Logs();
        parent::__construct($level,$bubble);
    }

    /**
     * Get logs storage
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getLogsStorage()
    {
        return $this->dbModel;
    }

    /**
     * Write log record
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record): void
    {       
        $context = (\is_array($record['context']) == true) ? \json_encode($record['context']) : null;
        $extra = (\is_array($record['extra']) == true) ? \json_encode($record['extra']) : null;
        
        $data = [
            'channel'      => $record['channel'],
            'level'        => $record['level'],
            'level_name'   => $record['formatted']['level_name'],
            'message'      => $record['formatted']['message'],
            'context'      => $context,
            'extra'        => $extra,
            'date_created' => DateTime::getTimestamp()
        ];
            
        $this->dbModel->create($data);
    }
}
