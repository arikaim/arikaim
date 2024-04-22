<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Blog\Controllers;

use Arikaim\Core\Db\Model;
use Arikaim\Core\Controllers\Controller;

/**
 * Blog controler
*/
class Blog extends Controller
{
    /**
     * Show pages
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function showBlogPostPage($request, $response, $data) 
    {
        $slug = $data->get('slug',null);
        $post = Model::Posts('blog')->findPost($slug, null);  

        if ($post == null) {
            return $this->pageNotFound($response,$data->toArray());
        } 

        if ($post->status != $post->ACTIVE()) {
            // post not published
            return $this->pageNotFound($response,$data->toArray());
        }

        if ($post->isDeleted() == true) {
            // post is deleted
            return $this->pageNotFound($response,$data->toArray());
        }

        $data['uuid'] = $post->uuid;   
        $metaTitle = empty($post->meta_title) ? $post->title : $post->meta_title;

        $this->get('page')->head()            
            ->param('title',$metaTitle)
            ->param('description',$post->meta_description) 
            ->param('keywords',$post->meta_keywords)      
            ->applyTwitterProperty('title',$metaTitle)   
            ->applyTwitterProperty('description',$post->meta_description)   
            ->applyOgProperty('title',$metaTitle)   
            ->applyOgProperty('description',$post->meta_description)                
            ->ogUrl($this->getUrl($request))         
            ->ogType('website') 
            ->twitterSite($this->getUrl($request));              
    } 
}
