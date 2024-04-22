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
    /**
     * Property type text names
     *
     * @var array
     */
    const TYPES = [
        'text',
        'number',
        'custom',
        'boolean',
        'list',
        'class',
        'password',
        'url',
        'text-area',
        'group',
        'oauth',
        'language-dropdown',
        'image',
        'key',
        'price',
        'file',
        'date',
        'time',
        'time-interval'
    ];

    /**
     * Property name
     *
     * @var string
     */
    protected $name;
    
    /**
     * Property id
     *
     * @var string|null
     */
    protected $id = null;

    /**
     * Property value
     *
     * @var mixed|null
     */
    protected $value = null;

    /**
     * Dropdown items
     *
     * @var array
     */
    protected $items = [];

    /**
     * Group name
     *
     * @var string|null
     */
    protected $group = null;

    /**
     * Default value
     *
     * @var mixed|null
     */
    protected $default = null;

    /**
     * Property title
     *
     * @var string|null
     */
    protected $title = null;

    /**
     * Property type
     *
     * @var integer
     */
    protected $type = 0;

    /**
     * Property description
     *
     * @var string|null
     */
    protected $description = null;

    /**
     * Property required atribute
     *
     * @var boolean
     */
    protected $required = false;

    /**
     * Property help
     *
     * @var string|null
     */
    protected $help = null;

    /**
     * Readonly attribute
     *
     * @var boolean
     */
    protected $readonly = false;

    /**
     * Hidden attribute
     *
     * @var boolean
     */
    protected $hidden = false;

    /**
     * Display type
     *
     * @var string|null
     */
    protected $displayType = null;

    /**
     * Constructor
     *
     * @param string $name  
     * @param array|null $data
     */
    public function __construct(string $name, ?array $data = null) 
    {
        $this->name = $name;    
        if (\is_array($data) == true) {
            $this->applyData($data);
        }
    }

    /**
     * Get readonly attribute
     *
     * @return boolean
     */
    public function getReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * Get readonly attribute
     *
     * @return boolean
     */
    public function isReadonly(): bool
    {
        return (empty($this->readonly) == true) ? false : $this->readonly;
    }

    /**
     * Return true if property is group
     *
     * @return boolean
    */
    public function isGroup(): bool
    {
        return ($this->type == PropertyInterface::GROUP);
    }

    /**
     * Get hidden attribute
     *
     * @return boolean
    */
    public function isHidden(): bool
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
     * Set property items
     *
     * @param array $items
     * @return Property
    */
    public function items(array $items)
    {
        $this->items = $items;

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
        $this->type = (\is_string($type) == true) ? $this->getTypeId($type) : $type;

        return $this;
    }

    /**
     * Set display type
     *
     * @param string|null $displayType
     * @return Property
     */
    public function displayType(?string $displayType)
    {
        $this->displayType = $displayType;
        return $this;
    }

    /**
     * Set readonly attribute
     *
     * @param boolean $readonly
     * @return Property
    */
    public function readonly(bool $readonly)
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
    public function hidden(bool $hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Get hidden property
     *
     * @return boolean
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Set property title
     *
     * @param string|null $title
     * @return Property
    */
    public function title(?string $title)
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
    public function required(bool $required)
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
     * @param string|null $description
     * @return Property
     */
    public function description(?string $description)
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
    public function help(?string $help)
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
    public function name(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set property id
     *
     * @param string $id
     * @return Property
     */
    public function id(?string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set property group
     *
     * @param string $name
     * @return Property
     */
    public function group(string $name)
    {
        $this->group = $name;
        return $this;
    }

    /**
     * Get type id
     *
     * @param string|int $type
     * @return int|null
     */
    public function getTypeId($type): ?int
    {
        $key = \array_search($type,Self::TYPES);       
        return ($key !== false) ? $key : null;
    }

    /**
     * Get type index
     *
     * @param string $type
     * @return integer|null
     */
    public static function getTypeIndex(string $type): ?int
    {
        $key = \array_search($type,Self::TYPES);       
        return ($key !== false) ? $key : null;
    }

    /**
     * Return property name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get property id.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Return property items.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Return property required attribute.
     *
     * @return boolean
     */
    public function getRequired(): bool
    {
        return (empty($this->required) == true) ? false : $this->required;
    }

    /**
     * Return property group.
     *
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * Return property value.
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return ($this->value === null) ? $this->getDefault() : $this->value;
    }

    /**
     * Get property default value.
     *
     * @return mixed|null
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
    public function getTitle(): ?string
    {
        return (empty($this->title) == true) ? $this->name : $this->title;
    }

    /**
     * Get property description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get property type
     *
     * @return integer|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * Get property type as text
     *
     * @return string
     */
    public function getTypeText(): string
    {       
        return Self::TYPES[$this->getType()] ?? 'unknow';
    }

    /**
     * Get property help
     *
     * @return string|null
     */
    public function getHelp(): ?string
    {
        return $this->help;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->getId(),
            'name'         => $this->getName(),        
            'value'        => $this->getValue(),
            'title'        => $this->getTitle(),
            'description'  => $this->description,
            'default'      => $this->getDefault(),
            'type'         => $this->getType(),
            'required'     => $this->required,
            'readonly'     => $this->isReadonly(),
            'hidden'       => $this->isHidden(),
            'items'        => $this->getItems(),
            'display_type' => $this->displayType,
            'group'        => $this->group,
            'help'         => $this->help
        ];
    }
    
    /**
     * Set property object params
     *
     * @param array $data
     * @return void
     */
    public function applyData(array $data)
    {
        foreach($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Create property obj from text
     *
     * @param string $text
     * @return Property|null
     */
    public static function createFromText(string $text)
    {
        $result = [];
        $tokens = \explode('|',$text);
        foreach ($tokens as $param) {
            $token = \explode('=',$param);
            $result[$token[0]] = $token[1];
        }
        
        return Self::create($result);
    }

    /**
     * Create property obj from array
     *
     * @param array $data
     * @return Property|null
     */
    public static function create(array $data)
    {
        $name = $data['name'] ?? null;
        if ($name === null) {
            return null;
        }
        $type = $data['type'] ?? 0;
     
        $property = new Self($name,$data);
        $property->type($type);

        return $property;
    }   
}
