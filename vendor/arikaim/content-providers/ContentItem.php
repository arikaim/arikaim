<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content;

use Arikaim\Core\Interfaces\Content\ContentItemInterface;
use Arikaim\Core\Interfaces\Content\ContentTypeInterface;
use Arikaim\Core\Interfaces\Content\FieldInterface;

/**
 *  Content item
 */
class ContentItem implements ContentItemInterface
{
    /**
     * Content data
     *
     * @var array
     */
    protected $data;

    /**
     * Content item type
     *
     * @var ContentTypeInterface
     */
    protected $type;

    /**
     * Content item id
     *
     * @var string
     */
    protected $id;

    /**
     * Constructor
     * 
     * @param array $data
     * @param ContentTypeInterface $type
     * @param string $id
     */
    public function __construct(array $data, ContentTypeInterface $type, string $id)
    {
        $this->data = $data;    
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Return true if content item is empty
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        foreach ($this->data as $key => $value) {
            if (empty($value) == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get title fields value
     *
     * @return string
     */
    public function getTitle(): string
    {
        $result = '';
        $titleFields = $this->type->getTitleFields();
        foreach ($titleFields as $field) {
            $result .= empty($result) ? $this->getValue($field) : ' ' . $this->getValue($field);
        }

        return \trim($result ?? '');
    }

    /**
     * True if field exist
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name) 
    {      
        return \array_key_exists($name,$this->data);
    }

    /**
     * Get field value
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {      
        return $this->data[$name] ?? null;
    }

    /**
     * Get content type
     *
     * @return ContentTypeInterface
     */
    public function getType(): ContentTypeInterface 
    {
        return $this->type;
    }

    /**
     * Run action
     *
     * @param string $name
     * @param array|null $options
     * @return mixed
     */
    public function runAction(string $name, ?array $options = [])
    {
        $actions = $this->actions();
        if (isset($actions[$name]) == false) {
            return false;
        }

        return $actions[$name]->execute($this,$options);     
    }

    /**
     * Create content item
     *
     * @param array $data
     * @param ContentTypeInterface $type
     * @param string $id
     * @return mixed
     */
    public static function create(array $data, ContentTypeInterface $type, string $id)
    {
        return new Self($data,$type,$id);
    } 

    /**
     * Get actions
     *
     * @return array
     */
    public function actions(): array
    {
        return $this->type->getActions();
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function fields(): array
    {
        $fields = $this->type->getFields();
        $result = [];
        
        foreach($fields as $field) {
            $value = $this->data[$field->getName()] ?? null;
            $field->setValue($value);
            $result[] = $field;
        }

        return $result;
    }

    /**
     * Get field value
     *
     * @param string $fieldName
     * @param mixed $default
     * @return mixed
     */
    public function getValue(string $fieldName, $default = null)
    {
        $field = $this->field($fieldName);
        if ($field == null) {
            return $this->data[$fieldName] ?? $default;
        }
        
        return $field->getValue();
    }

    /**
     * Get content field
     *
     * @param string $fieldName
     * @return FieldInterface|null
     */
    public function field(string $fieldName): ?FieldInterface
    {
        $field = $this->type->getField($fieldName);
        if ($field === null) {
            return null;
        }
        $value = $this->data[$fieldName] ?? null;
        $field->setValue($value);

        return $field;
    }

    /**
     * Set field value
     *
     * @param string $fieldName
     * @param mixed $value
     * @return void
     */
    public function setValue(string $fieldName, $value): void
    {
        $this->data[$fieldName] = $value;
    }

    /**
     * Get content item id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }   

    /**
     * Get data array
     *
     * @return array
     */
    public function getDataArray(): array
    {
        return $this->data; 
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        foreach($this->fields() as $field) {
            $name = $field->getName();
            $result[$name] = $this->data[$name] ?? null;
        }

        return $result;
    } 
}
