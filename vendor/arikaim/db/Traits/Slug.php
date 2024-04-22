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

use Arikaim\Core\Utils\Utils;

/**
 * Create slug
 * 
 * Custom slug source column 
 * 
 *  protected $slugSourceColumn = 'column name';
 * 
 * Custom slug column name
 *  
 *  protected $slugColumnName = 'column name';
 * 
 * Custom slug separator
 *  
 *  protected $slugSeparator = 'separtor';
*/
trait Slug 
{    
    /**
     * Default slug source column
     *
     * @var string
     */
    protected static $DEFAUL_SLUG_SOURCE_COLUMN = 'title';
    
    /**
     * Default slug column name
     *
     * @var string
     */
    protected static $DEFAUL_SLUG_COLUMN = 'slug';

    /**
     * Default slug column name
     *
     * @var string
     */
    protected static $DEFAUL_SLUG_SEPARATOR = '-';

    /**
     * Set model event on saving
     *
     * @return void
     */
    public static function bootSlug()
    {
        static::saving(function($model) {   
            $model = Self::saveSlug($model);
        });     

        static::creating(function($model) { 
            $model = Self::saveSlug($model);
        });
    }

    /**
     * Get slug value
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slugPrefix ?? '' . $this->attributes[$this->slugColumnName ?? static::$DEFAUL_SLUG_COLUMN] . $this->slugSuffix ?? '';
    }

    /**
     * Save slug
     *
     * @param Model $model   
     * @return Model
     */
    public static function saveSlug($model): object
    {        
        if ($model->{$model->slugSourceColumn ?? static::$DEFAUL_SLUG_SOURCE_COLUMN} !== null) {                   
            $model->{$model->slugColumnName ?? static::$DEFAUL_SLUG_COLUMN} = $model->createSlug(
                $model->{$model->slugSourceColumn ?? static::$DEFAUL_SLUG_SOURCE_COLUMN}           
            );
        }              
       
        return $model;
    }

    /**
     * Set slug field
     *
     * @param string|null $text
     * @return void
     */
    public function setSlug(?string $text = null): void
    {
        $text = (empty($text) == true) ? $this->{$this->slugSourceColumn ?? static::$DEFAUL_SLUG_SOURCE_COLUMN} : $text;

        $this->{$this->slugColumnName ?? static::$DEFAUL_SLUG_COLUMN} = $this->createSlug($text);
    }

    /**
     * Create slug from text
     *
     * @param string $text
     * @param string|null $separator
     * @return string
     */
    public function createSlug(string $text, ?string $separator = null): string
    {       
        return Utils::slug($text,$separator ?? $this->slugSeparator ?? static::$DEFAUL_SLUG_SEPARATOR);
    }

    /**
     * Find by slug scope
     *
     * @param Builder      $query
     * @param string       $slug
     * @param integer|null $userId
     * @return Builder
     */
    public function scopeSlugQuery($query, string $slug, ?int $userId = null)
    {
        $query->where($this->slugColumnName ?? static::$DEFAUL_SLUG_COLUMN,'=',$slug);

        return (empty($userId) == false) ? $query->where('user_id','=',$userId) : $query;        
    }

    /**
     * Find model by slug
     *
     * @param string $slug
     * @return Model|null
     */
    public function findBySlug(string $slug): ?object
    {  
        return $this
            ->where($this->slugColumnName ?? static::$DEFAUL_SLUG_COLUMN,'=',$slug)
            ->orWhere($this->slugColumnName ?? static::$DEFAUL_SLUG_COLUMN,'=',$this->createSlug($slug))
            ->first();       
    }
}
