<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Collection\Arrays;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Interfaces\OptionsStorageInterface;
use Exception;

/**
 * Options database model
 */
class Options extends Model implements OptionsStorageInterface
{    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'key',
        'value',
        'auto_load',
        'extension'
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'options';

    /**
     * Read option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function read(string $key, $default = null) 
    {
        try {
            $model = $this->where('key','=',$key)->first();
        } catch (Exception $e) {
            return $default;
        }
        $value = ($model == null) ? $default : $model->value;

        return ($value === null || $value == '') ? $default : $value;  
    }

    /**
     * Create option, if option exists return false
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $autoLoad
     * @param string|null $extension
     * @return boolean
     */
    public function createOption(string $key, $value, bool $autoLoad = false, ?string $extension = null): bool
    {
        return ($this->hasOption($key) == true) ? false : $this->saveOption($key,$value,$extension,$autoLoad);       
    }

    /**
     * Return true if option name exist
     *
     * @param string $key
     * @return boolean
     */
    public function hasOption(string $key): bool
    {
        try {
            $model = $this->where('key','=',$key)->first();
        } catch (Exception $e) {
            return false;
        }

        return ($model != null);
    }

    /**
     * Save option
     *
     * @param string $key
     * @param mixed $value
     * @param string $extension
     * @param bool $autoLoad
     * @return bool
     */
    public function saveOption(string $key, $value, ?string $extension = null, bool $autoLoad = false): bool 
    {
        $key = \trim(\str_replace('_','.',$key));
        $type = \gettype($value);

        if (\is_array($value) == true) {            
            $value = \json_encode($value);
            $type = 'json';
        }
        if (\is_string($value) == true) {
            $type = (Utils::isJson($value) == true) ? 'json' : $type;
        }

        $data = [
            'key'       => $key,
            'value'     => $value,
            'type'      => $type,
            'auto_load' => ($autoLoad == true) ? 1 : 0,
            'extension' => $extension
        ];
 
        if ($this->hasOption($key) == true) {
            $result = $this->where('key','=',$key)->update($data);
            return ($result !== false);
        }

        return ($this->create($data) != null);
    }

    /**
     * Load options
     *
     * @return array
     */
    public function loadOptions(): array
    {             
        try {
            $model = $this->select('key','value')->get();
            $options = $model->mapWithKeys(function ($item) {
                return [$item['key'] => $item['value']];
            })->toArray(); 

            return $options;
                              
        } catch (Exception $e) {
            return [];
        }
      
        return [];
    }

    /**
     * Search for options
     *
     * @param string|null $searchKey
     * @param bool $compactKeys
     * @return array
     */
    public function searchOptions(?string $searchKey, bool $compactKeys = false): array
    {
        $options = [];
        $model = $this->where('key','like',$searchKey . '%')->select('key','value')->get();
        
        if (\is_object($model) == false) {
            return [];
        }
       
        $options = $model->mapWithKeys(function($item) {
            return [$item['key'] => $item['value']];
        })->toArray(); 
        
        if ($compactKeys == true) {          
            return $options;
        }

        $values = Arrays::getValues($options,$searchKey);
        $result = [];
        
        foreach ($values as $key => $value) {
            $result = Arrays::setValue($result,$key,$value,'.');
        }      

        return $result;      
    }

    /**
     * Remove option
     *
     * @param string|null $key
     * @param string|null $extension
     * @return bool
     */
    public function remove(?string $key = null, ?string $extension = null): bool
    {
        $model = (empty($extension) == false) ? $this->where('extension','=',$extension) : $this;
        $model = (empty($key) == false) ? $this->where('key','=',$extension) : $model;

        $result = (bool)$model->delete();

        return $result;
    }

    /**
     * Get extension options
     *
     * @param string $extensioName
     * @return mixed
     */
    public function getExtensionOptions(string $extensioName)
    {
        return $this->where('extension','=',$extensioName)->select('key','value')->get();
    }
}
