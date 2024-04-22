<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Category\Subscribers;

use Arikaim\Core\Events\EventSubscriber;
use Arikaim\Core\Interfaces\Events\EventSubscriberInterface;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Routes\Route;

/**
 * Sitemap subscriber class
 */
class SitemapSubscriber extends EventSubscriber implements EventSubscriberInterface
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {       
        $this->subscribe('sitemap.pages');
    }
    
    /**
     * Subscriber code.
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function execute($event)
    {     
        $params = $event->getParameters();
        $language = $params['language'] ?? 'en';

        if ($params['name'] == 'categoryPage') {
            return $this->getCategoryPages($params,$language);       
        }  

        $url = Route::getRouteUrl($params['pattern']);

        return (empty($url) == false) ? [$url] : null;  
    }

    /**
     * Get category pages url
     *
     * @param array $route
     * @param string $language
     * @return array
     */
    public function getCategoryPages($route, $language = 'en')
    {
        $pages = [];
        $category = Model::Category('category',function($model) {                
            return $model->getActive()->get();           
        });

        foreach ($category as $item) {
            $slug = $item->getSlug($item);

            if (empty($slug) == false) {
                $url = Route::getRouteUrl($route['pattern'],[
                    'slug' => $slug
                ]);
                $pages[] = $url;
            }           
        }     

        return $pages;
    }
    
}
