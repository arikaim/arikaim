<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Content\Type;

use Arikaim\Core\Interfaces\Content\FieldInterface;
use Exception;

/**
 *  Content type field
 */
class Field implements FieldInterface
{
    /**
     * Field name
     *
     * @var string
     */
    protected $name;

    /**
     * Field type
     *
     * @var string
     */
    protected $type;

    /**
     * Field value
     *
     * @var mixed
     */
    protected $value = null;
  
    /**
     * Field title(label)
     *
     * @var string|null
     */
    protected $title = null;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $type
     * @param string|null $title
     * @param mixed $value
     */
    public function __construct(string $name, string $type, ?string $title = null, $value = null)
    {
        if (Self::isValidType($type) == false) {
            throw new Exception('Not vlaid field type '. $type,1);            
        }

        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
        $this->title = $title;
    }

    /**
     * Get field name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get field title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Get field type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set field value
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * Create field
     *
     * @param string $name
     * @param string $type
     * @param string|null $title
     * @param mixed $value
     * @return FieldInterface
     */
    public static function create(string $name, string $type, ?string $title = null, $value = null): FieldInterface
    {
        return new Self($name,$type,$title,$value);
    }

    /**
     * Return true if field type is valid
     *
     * @param string $type
     * @return boolean
     */
    public static function isValidType(string $type): bool
    {
        return \in_array($type,FieldInterface::TYPES_LIST);
    }
}
