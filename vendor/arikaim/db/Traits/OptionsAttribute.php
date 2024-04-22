<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

/**
 * Options attribute trait
*/
trait OptionsAttribute 
{        
    /**
     * Get options column name
     *
     * @return string
     */
    public function getOptionsColumnName(): string
    {
        return $this->optionsColumnName ?? 'options';
    }

    /**
     * Mutator (get) for options attribute.
     *
     * @return array
     */
    public function getOptionsAttribute()
    {
        $options = $this->attributes[$this->getOptionsColumnName()] ?? null;

        return (empty($options) == true) ? [] : \json_decode($options,true);
    }

    /**
     * Get option from options array
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Save option
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function saveOption(string $key, $value): bool
    {
        $options = $this->getOptionsAttribute();
        $options[$key] = $value;
      
        $encoded = \json_encode(
            $options,
            JSON_PRETTY_PRINT | 
            JSON_UNESCAPED_UNICODE | 
            JSON_UNESCAPED_SLASHES |
            JSON_NUMERIC_CHECK 
        );

        $result = $this->update([
            $this->getOptionsColumnName() => $encoded
        ]);

        return ($result !== false);
    }

    /**
     * Save options
     *
     * @param array $options
     * @return boolean
     */
    public function saveOptions(array $options): bool
    {
        $result = $this->update([
            $this->getOptionsColumnName() => \json_encode($options)
        ]);

        return ($result !== false);
    }
}
