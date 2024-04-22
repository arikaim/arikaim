<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db;

use Illuminate\Database\Schema\Blueprint;

use Arikaim\Core\Db\Interfaces\BlueprintPrototypeInterface;

/**
 * Extended Blueprint with column prototypes
*/
class TableBlueprint extends Blueprint
{
    /**
     * Get commands
     *
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;        
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;        
    }

    /**
     * Add a new command to the blueprint.
     *
     * @param  string $name
     * @param  array  $parameters
     * @return \Illuminate\Support\Fluent
     */
    public function addCommand($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    /**
     * Add a new foregn command to the blueprint.
     *
     * @param  \Illuminate\Support\Fluent  $foreign
     * @return \Illuminate\Support\Fluent
    */
    public function addForeign($foreign)
    {
        $this->commands[count($this->commands) - 1] = $foreign;

        return $foreign;
    }

    /**
     * Add a new index command to the blueprint.
     *
     * @param  string  $type
     * @param  string|array  $columns
     * @param  string  $index
     * @param  string|null  $algorithm
     * @return \Illuminate\Support\Fluent
     */
    public function indexCommand($type, $columns, $index, $algorithm = null)
    {
        $columns = (array) $columns;
        $index = $index ?: $this->createIndexName($type, $columns);

        return $this->addCommand(
            $type, compact('index', 'columns', 'algorithm')
        );
    }

    /**
     * Build column blueprint prototype
     *
     * @param string $name
     * @param mixed|null $options
     * @return void
     */
    public function prototype(string $name, $options = null): void
    {
        $class = $this->resolveColumnPrototypeClass($name); 
        $this->buildPrototype($class,$options);
    }

    /**
     * Build table blueprint prototype
     *
     * @param string $name
     * @param mixed|null $options
     * @return void
     */
    public function tablePrototype(string $name, $options = null): void
    {
        $class = '\\Arikaim\\Core\\Db\\Prototypes\\Table\\' . ucfirst($name);
        $this->buildPrototype($class,$options);
    }

    /**
     * Build blueprint prototype
     *
     * @param string $class
     * @param mixed $options
     * @return void
     */
    protected function buildPrototype(string $class, $options): void
    {      
        if (\class_exists($class) == true) {               
            $prototype = new $class();
            if ($prototype instanceof BlueprintPrototypeInterface) {   
                $options = (\is_array($options) == false) ? [$options] : $options;
                $prototype->build($this,...$options);                                            
            }           
        }
    }

    /**
     * Resolve column prototype class name
     *
     * @param string $name
     * @return string
     */
    protected function resolveColumnPrototypeClass(string $name): string
    {
        if (\class_exists($name) == true) {    
            return $name;
        }
        $tokens = \explode('_',$name);
        $class_name = '';
        foreach ($tokens as $item) {
            $class_name .= \ucfirst($item);
        }

        return '\\Arikaim\\Core\\Db\\Prototypes\\Column\\' . $class_name;
    }

    /**
     * Call prototype method 
     *
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (\substr($name,0,5) == 'table') {          
            $this->tablePrototype(\substr($name,5),$arguments);
        } else {
            $this->prototype($name,$arguments);
        }        
    }
}   
