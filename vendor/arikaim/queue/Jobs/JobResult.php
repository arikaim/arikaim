<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Queue\Jobs;

/**
 * Job execution result
 */
class JobResult
{   
    /**
     * Error
     *
     * @var string|null
     */
    protected $error = null;

    /**
     * Result fields
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Constructor
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
        $this->error = null;
    }

    /**
     * Create
     *
     * @param array $fields
     * @return Self
     */
    public static function create(array $fields = [])
    {
        return new Self($fields);
    }

    /**
     * Set field
     *
     * @param string $name
     * @param mixed $value
     * @return Self
     */
    public function field(string $name, $value)
    {
        $this->fields[$name] = $value;
        return $this;
    }

    /**
     * Get field value
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->fields[$name] ?? $default;
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Return true if has error
     *
     * @return boolean
     */
    public function hasError(): bool
    {
        return !empty($this->error);
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array 
    {
        return [
            'error'  => $this->error,
            'fields' => $this->fields
        ];
    }

    /**
     * Set error
     *
     * @param string|null $error
     * @return Self
     */
    public function error(?string $error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }
}
