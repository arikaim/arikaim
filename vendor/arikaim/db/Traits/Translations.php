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

use Arikaim\Core\View\Html\HtmlComponent;

/**
 *  Translations trait      
*/
trait Translations 
{           
    /**
     * Get translation refernce attribute name 
     *
     * @return string|null
     */
    public function getTranslationReferenceAttributeName()
    {
        return (isset($this->translationReference) == true) ? $this->translationReference : null;
    }

    /**
     * Get translation miodel class
     *
     * @return string|null
     */
    public function getTranslationModelClass()
    {
        return (isset($this->translationModelClass) == true) ? $this->translationModelClass : null;
    }

    /**
     * HasMany relation
     *
     * @return mixed
     */
    public function translations()
    {       
        return $this->hasMany($this->getTranslationModelClass());
    }

    /**
     * Get translations query
     *
     * @param string|mull $language
     * @return Builder
     */
    public function getTranslationsQuery($language = null)
    {
        $class = $this->getTranslationModelClass();
        $model = new $class();
        $language = (empty($language) == true) ? HtmlComponent::getLanguage() : $language;
        
        return $model->where('language','=',$language);
    }

    /**
     * Get translation model
     *
     * @param string $language
     * @return Model|false
     */
    public function translation($language = null, $query = false)
    {
        $language = (empty($language) == true) ? HtmlComponent::getLanguage() : $language;
        $model = $this->translations()->getQuery()->where('language','=',$language);
        $model = ($query == false) ? $model->first() : $model;

        return (is_object($model) == false) ? false : $model;
    }

    /**
     * Create or update translation 
     *
     * @param string|integer|null $id
     * @param array $data
     * @param string $language
     * @return Model
     */
    public function saveTranslation(array $data, $language = null, $id = null)
    {
        $language = (empty($language) == true) ? HtmlComponent::getLanguage() : $language;
        $model = (empty($id) == true) ? $this : $this->findById($id);     
        $reference = $this->getTranslationReferenceAttributeName();

        $data['language'] = $language;
        $data[$reference] = $model->id;

        $translation = $model->translation($language);
    
        if ($translation === false) {
            return $model->translations()->create($data);
        } 
        $translation->update($data);  
        
        return $translation;
    }

    /**
     * Delete translation
     *
     * @param string|integer|null $id
     * @param string $language
     * @return boolean
     */
    public function removeTranslation($id = null, $language = null)
    {
        $language = (empty($language) == true) ? HtmlComponent::getLanguage() : $language;
        $model = (empty($id) == true) ? $this : $this->findById($id);     
        $model = $model->translation($language);

        return (is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Delete all translations
     *
     * @param string|integer|null $id
     * @return boolean
     */
    public function removeTranslations($id = null)
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        $model = $model->translations();

        return (is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Find Translation
     *
     * @param string $attributeName
     * @param mixed $value
     * @return void
     */
    public function findTranslation($attributeName, $value)
    {     
        $class = $this->getTranslationModelClass();
        $model = new $class();

        $model = $model->whereIgnoreCase($attributeName,trim($value));

        return $model->first();
    }
}
