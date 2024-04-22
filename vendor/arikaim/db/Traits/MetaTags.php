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
 * Page meta tags trait      
*/
trait MetaTags 
{           
    /**
     * Get meta tags field values
     *
     * @param Model|null $model
     * @param array|null $default
     * @return array
     */
    public function getMetaTags($model = null, ?array $default = []): array
    {
        $model = $model ?? $this;

        return [
            'title'       => (empty($model->meta_title) == false) ? $model->meta_title : $default['title'] ?? '',
            'description' => (empty($model->meta_description) == false) ? $model->meta_description : $default['description'] ?? '',
            'keywords'    => (empty($model->meta_keywords) == false) ? $model->meta_keywords : $default['keywords'] ?? ''
        ];
    }
}
