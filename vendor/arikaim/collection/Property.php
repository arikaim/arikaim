<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Collection;

use Arikaim\Core\Collection\Interfaces\PropertyInterface;

/**
 * Property descriptior
 */
class Property implements PropertyInterface
{
    const TEXT      = 0;
    const NUMBER    = 1;
    const BOOLEAN   = 3;
    const LIST      = 4;
    const PHP_CLASS = 5;
    const PASSWORD  = 6;
    const URL       = 7;
    const TEXT_AREA = 8;

    /**
     * Property type text names
     *
     * @var array
     */
    private $typeNames = [
        'text',
        'number',
        'boolean',
        'list',
        'class',
        'password',
        'url',
        'text-area'
    ];

    /**
     * Property name
     *
     * @var string
     */
    protected $name;
    
    /**
     * Property value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Default value
     *
     * @var mixed
     */
    protected $default;

    /**
     * Property title
     *
     * @var string
     */
    protected $title;

    /**
     * Property type
     *
     * @var integer
     */
    protected $type;

    /**
     * Property description
     *
     * @var string
     */
    protected $description;

    /**
     * Property required atribute
     *
     * @var boolean
     */
    protected $required;

    /**
     * Property help
     *
     * @var string
     */
    protected $help;

    /**
     * Readonly attribute
     *
     * @var boolean
     */
    protected $readonly;

    /**
     * Hidden attribute
     *
     * @var boolean
     */
    protected $hidden;

    /**
     * Constructor
     *
     * @param string|null $name
     * @param mixed|null $value
     * @param mixed|null $default
     * @param string|null $type
     * @param string|null $title
     * @param string|null $description
     * @param boolean $required
     * @param string|null $help
     */
    public function __construct($name = null, $value = null, $default = null, $type = Self::TEXT, $title = null, $description = null, $required = false, $help = null) 
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
        $this->title = $title;
        $this->default = $default;
        $this->description = $description;
        $this->required = $required;
        $this->help = $help;
    }

    /**
     * Get readonly attribute
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return (empty($this->readonly) == true) ? false : $this->readonly;
    }

    /**
     * Get hidden attribute
     *
     * @return boolean
    */
    public function isHidden()
    {
        return (empty($this->hidden) == true) ? false : $this->hidden;
    }

    /**
     * Set property value
     *
     * @param mixed|null $value
     * @return Property
    */
    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set property type
     *
     * @param string|integer $type
     * @return Property
    */
    public function type($type)
    {
        $this->type = (is_string($type) == true) ? $this->getTypeId($type) : $type;
        return $this;
    }

    /**
     * Set readonly attribute
     *
     * @param boolean $readonly
     * @return Property
    */
    public function readonly($readonly)
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * Set hidden attribute
     *
     * @param boolean $hidden
     * @return Property
    */
    public function hidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Set property title
     *
     * @param string $title
     * @return Property
    */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set property required attribute
     *
     * @param boolean $required
     * @return Property
    */
    public function required($required)
    {
        $this->required = (boolean)$required;
        return $this;
    }

    /**
     * Set property default
     *
     * @param mixed|null $default
     * @return Property
    */
    public function default($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Set property description
     *
     * @param string $description
     * @return Property
     */
    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set property help
     *
     * @param string $help
     * @return Property
     */
    public function help($help)
    {
        $this->help = $help;
        return $this;
    }

    /**
     * Set property name
     *
     * @param string $name
     * @return Property
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get type id
     *
     * @param string $type
     * @return void
     */
    public function getTypeId($type)
    {
        $key = array_search($type,$this->typeNames);       
        return ($key !== false) ? $key : null;
    }

    /**
     * Return property name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return property required attribute.
     *
     * @return boolean
     */
    public function getRequired()
    {
        return (empty($this->required) == true) ? false : $this->required;
    }

    /**
     * Return property value.
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return (is_null($this->value) == true) ? $this->getDefault() : $this->value;
    }

    /**
     * Return property version.
     *
     * @return string|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Return property display name.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return (empty($this->title) == true) ? $this->name : $this->title;
    }

    /**
     * Get property description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get property type
     *
     * @return integer|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get property type as text
     *
     * @return string
     */
    public function getTypeText()
    {
        $type = $this->getType();
        return (isset($this->typeNames[$type]) == true) ? $this->typeNames[$type] : 'unknow';
    }

    /**
     * Get property help
     *
     * @return string|null
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'value' => $this->value,
            'title' => $this->getTitle(),
            'description' => $this->description,
            'default' => $this->default,
            'type' => $this->type,
            'required' => $this->required,
            'readonly' => $this->isReadonly(),
            'hidden' => $this->isHidden(),
            'help' => $this->help,
        ];
    }
    
    /**
     * Create property obj from text
     *
     * @param string $text
     * @return Property
     */
    public static function createFromText($text)
    {
        $result = [];
        $tokens = explode('|',$text);
        foreach ($tokens as $param) {
            $token = explode('=',$param);
            $result[$token[0]] = $token[1];
        }
        
        return Self::create($result);
    }

    /**
     * Create property obj from array
     *
     * @param array $data
     * @return Property
     */
    public static function create(array $data)
    {
        $name = (isset($data['name']) == true) ? $data['name'] : null;
        $value = (isset($data['value']) == true) ? $data['value'] : null;
        $required = (isset($data['required']) == true) ? $data['required'] : false;
        $default = (isset($data['default']) == true) ? $data['default'] : null;
        $type = (isset($data['type']) == true) ? $data['type'] : Self::TEXT;
        $title = (isset($data['title']) == true) ? $data['title'] : null;
        $description = (isset($data['description']) == true) ? $data['description'] : null;
        $help = (isset($data['help']) == true) ? $data['help'] : null;
        $readonly = (isset($data['readonly']) == true) ? $data['readonly'] : false;
        $hidden = (isset($data['hidden']) == true) ? $data['hidden'] : false;

        $property = new Self($name,$value,$default,$type,$title,$description,$required,$help);
        
        return $property->readonly($readonly)->hidden($hidden);
    }
}
