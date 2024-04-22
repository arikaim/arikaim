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

use Arikaim\Core\Http\Session;

/**
 * Translations trait      
*/
trait Translations 
{           
    /**
     * Current language
     *
     * @var string|null
     */
    protected $currentLanguage;

    /**
     * Boot trait.
     *
     * @return void
     */
    public static function bootTranslations()
    {
        static::retrieved(function($model) {           
            $language = $model->getCurrentLanguage();
            $model->translateAttributes($language);          
        });        
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getCurrentLanguage(): string
    {
        return $this->currentLanguage ?? Session::get('language','en');
    }

    /**
     * Set language
     *
     * @param string|null $language
     * @return void
     */
    public function setLanguage(?string $language): void
    {
        $this->currentLanguage = $language ?? 'en';
        
        // update translated attributes
        $this->translateAttributes($language);       
    }

    /**
     * Return translated value
     *
     * @param string $attribute
     * @param string|null $language
     * @return string|null
     */
    public function translateAttribute(string $attribute, ?string $language = null): ?string
    {
        $language = $language ?? $this->getCurrentLanguage();
        $translation = $this->translation($language);

        return ($translation === false) ? null : $translation->$attribute ?? null;          
    }

    /**
     * Translate attributes
     *
     * @param string $language
     * @return boolean
     */
    public function translateAttributes(string $language): bool
    {
        $translation = $this->translation($language);
        if ($translation === false) {
            return false;
        }

        $list = $this->getTranslatedAttributes();
        foreach ($list as $attribute) {     
            $translatedValue = (empty($translation->$attribute) == false) ? $translation->$attribute : $this->attributes[$attribute] ?? null;    
            $this->attributes[$attribute] = $translatedValue;
        }

        return true;
    }

    /**
     * Get translation attributes
     *
     * @return array
     */
    public function getTranslatedAttributes(): array
    {
        return $this->translatedAttributes ?? [];
    }

    /**
     * Get translation refernce attribute name 
     *
     * @return string|null
     */
    public function getTranslationReferenceAttributeName(): ?string
    {
        return $this->translationReference ?? null;
    }

    /**
     * Get translation miodel class
     *
     * @return string|null
     */
    public function getTranslationModelClass(): ?string
    {
        return $this->translationModelClass ?? null;
    }

    /**
     * HasMany relation
     *
     * @return Relation|null
     */
    public function translations()
    {       
        return $this->hasMany($this->getTranslationModelClass(),$this->getTranslationReferenceAttributeName());
    }

    /**
     * Get translations query
     *
     * @param string|null $language
     * @return Builder
     */
    public function getTranslationsQuery(?string $language = null)
    {
        $class = $this->getTranslationModelClass();
        $model = new $class();
        $language = $language ?? $this->getCurrentLanguage();
        
        return $model->where('language','=',$language);
    }

    /**
     * Get translation model
     *
     * @param string|null $language
     * @param bool $query
     * @return Model|false
     */
    public function translation(?string $language = null, bool $query = false)
    {
        $language = $language ?? $this->getCurrentLanguage();
        $model = $this->translations()->getQuery()->where('language','=',$language);
        $model = ($query == false) ? $model->first() : $model;

        return ($model == null) ? false : $model;
    }

    /**
     * Create or update translation 
     *   
     * @param array $data
     * @param string|null $language
     * @param string|integer|null $id 
     * @return Model|false
     */
    public function saveTranslation(array $data, ?string $language = null, $id = null)
    {
        $language = $language ?? $this->getCurrentLanguage();
        $model = (empty($id) == true) ? $this : $this->findById($id);     
        $reference = $this->getTranslationReferenceAttributeName();

        $data['language'] = $language;
        $data[$reference] = $model->id;

        $translation = $model->translation($language);
        if ($translation === false) {
            return $model->translations()->create($data);
        } 
        $result = (bool)$translation->update($data);  
        
        return ($result === false) ? false : $translation;
    }

    /**
     * Delete translation
     *
     * @param string|integer|null $id
     * @param string|null $language
     * @return boolean
     */
    public function removeTranslation($id = null, ?string $language = null): bool
    {
        $language = $language ?? $this->getCurrentLanguage();
        $model = (empty($id) == true) ? $this : $this->findById($id);     
        $model = $model->translation($language);

        return ($model !== false) ? (bool)$model->delete() : false;
    }

    /**
     * Delete all translations
     *
     * @param string|integer|null $id
     * @return boolean
     */
    public function removeTranslations($id = null): bool
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        $model = $model->translations();

        return (\is_object($model) == true) ? (bool)$model->delete() : false;
    }

    /**
     * Find Translation
     *
     * @param string $attributeName
     * @param mixed $value
     * @param string|null $language
     * @return Model|null
     */
    public function findTranslation(string $attributeName, $value, ?string $language = null)
    {     
        $language = $language ?? $this->getCurrentLanguage();
        $class = $this->getTranslationModelClass();
        $model = new $class(); 
        $model = $model->where('language','=',$language)->where($attributeName,'=',\trim($value ?? ''));

        return $model->first();
    }
}
