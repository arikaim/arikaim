<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Table;

use Arikaim\Core\Collection\Collection;
use \Closure;

/**
 * Table class
 */
class Table extends Collection
{   
    /**
     * Table header
     *
     * @var array
     */
    protected $header;

    /**
     * Field separator
     *
     * @var string
     */
    protected $separator;

    /**
     * Constructor
     *
     * @param array      $data
     * @param array|null $header
     * @param string $separator
     */
    public function __construct(array $data, ?array $header = null, string $separator = ',')
    {       
        parent::__construct($data);

        $this->header = $header ?? [];
        $this->separator = $separator;
    }

    /**
     * Apply func to column
     *
     * @param string  $columnName
     * @param Closure $callback
     * @return object
     */
    public function apply(string $columnName, Closure $callback): object
    {
        $key = $this->getColumnKey($columnName);
        if ($key === false) {
            return $this;
        }

        foreach ($this->data as $index => $row) {
           $row[$key] = $callback($row[$key]);
           $this->data[$index] = $row;
        }

        return $this;
    }

    /**
     * Get field separator
     *
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * Set separator
     *
     * @param string $separator
     * @return void
     */
    public function separator(string $separator): void
    {
        $this->separator = $separator;
    }

    /**
     * Get header
     *
     * @return array
     */
    public function header(): array
    {
        return $this->header;
    }

    /**
     * Get table column
     *
     * @param string $name
     * @return object|null
     */
    public function column(string $name): ?object
    {
        $key = $this->getColumnKey($name);
        if ($key === false) {
            return null;
        }

        $data = \array_column($this->data,$key);    
        
        return new Self($data,[$name]);
    }

    /**
     * Filter table by column
     *
     * @param string $columnName
     * @param mixed $value
     * @return Self
     */
    public function filter(string $columnName, $value): object
    {
        $key = $this->getColumnKey($columnName);
        if ($key === false) {
            return $this;
        }

        $result = [];
        foreach ($this->data as $row) {
            if ($row[$key] == $value) {
               $result[] = $row; 
            }
        }

        return new Self($result,$this->header);
    }

    /**
     * Export table to csv
     *
     * @param string      $fileName
     * @param string|null $separator
     * @return boolean
     */
    public function saveCsv(string $fileName, ?string $separator = null): bool
    {
        $file = \fopen($fileName,'w');
        if ($file === false) {
            return false;
        }

        $separator = $separator ?? $this->separator;
        \fputcsv($file,$this->header,$separator);
        
        foreach ($this->data as $row) {
            \fputcsv($file,$row,$separator);
        }
           
        \fclose($file);

        return true;
    }

    /**
     * Get header key
     *
     * @param string $name
     * @return mixed
     */
    protected function getColumnKey(string $name)
    {
        return \array_search($name,$this->header);
    }
}
