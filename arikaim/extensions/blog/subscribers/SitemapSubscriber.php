<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Blog\Subscribers;

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
     * Subscriber code executed.
     *
     * @param EventInterface $event
     * @return void
     */
    public function execute($event)
    {     
        $params = $event->getParameters();
         
        if ($params['name'] == 'blogPostPage') {
            return $this->getBlogPostPages($params);       
        }  
    
        $url = Route::getRouteUrl($params['pattern']);

        return (empty($url) == false) ? [$url] : null;  
    }

    /**
     * Get blog post pages
     *
     * @param array $route
     * @return array
     */
    public function getBlogPostPages($route)
    {
        $result = [];
        $posts = Model::Posts('blog')->getActive()->get();               
        
        foreach ($posts as $item) {               
            $url = Route::getRouteUrl($route['pattern'],[
                'slug' => $item->slug
            ]);
         
            $result[] = $url;
        }      

        return $result;
    }
}
