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

use Arikaim\Core\Db\BlueprintPrototypeInterface;

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
     * Build column blueprint prototype
     *
     * @param string $name
     * @param mixed|null $options
     * @return void
     */
    public function prototype($name, $options = null)
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
    public function tablePrototype($name, $options = null)
    {
        $class = "\\Arikaim\\Core\\Db\\Prototypes\\Table\\" . ucfirst($name);
        $this->buildPrototype($class,$options);
    }

    /**
     * Build blueprint prototype
     *
     * @param string $class
     * @param mixed $options
     * @return void
     */
    protected function buildPrototype($class, $options)
    {      
        if (class_exists($class) == true) {               
            $prototype = new $class();
            if ($prototype instanceof BlueprintPrototypeInterface) {   
                $options = (is_array($options) == false) ? [$options] : $options;
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
    protected function resolveColumnPrototypeClass($name)
    {
        if (class_exists($name) == true) {    
            return $name;
        }
        $tokens = explode('_',$name);
        $class_name = "";
        foreach ($tokens as $item) {
            $class_name .= ucfirst($item);
        }

        return "\\Arikaim\\Core\\Db\\Prototypes\\Column\\" . $class_name;
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
        if (substr($name,0,5) == 'table') {          
            $this->tablePrototype(substr($name,5),$arguments);
        } else {
            $this->prototype($name,$arguments);
        }        
    }
}   
