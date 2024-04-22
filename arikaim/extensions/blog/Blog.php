<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Blog;

use Arikaim\Core\Extension\Extension;

/**
 * Blog extension
*/
class Blog extends Extension
{
    /**
     * Install extension routes, events, jobs
     *
     * @return void
    */
    public function install()
    {
        // Api routes
        $this->addApiRoute('POST','/api/blog/post/add','PostApi','add','session');   
        $this->addApiRoute('PUT','/api/blog/post/update','PostApi','update','session');   
        $this->addApiRoute('PUT','/api/blog/post/status','PostApi','setStatus','session');   
        $this->addApiRoute('DELETE','/api/blog/post/delete/{uuid}','PostApi','softDelete','session');  
        $this->addApiRoute('PUT','/api/blog/post/restore','PostApi','restore','session');  
        $this->addApiRoute('PUT','/api/blog/post/update/meta','PostApi','updateMetaTags','session');  
        $this->addApiRoute('PUT','/api/blog/post/update/image','PostApi','updateImage','session');  
        $this->addApiRoute('PUT','/api/blog/post/update/summary','PostApi','updateSummary','session');  
        $this->addApiRoute('DELETE','/api/blog/trash/empty','PostApi','emptyTrash','session');  
        // Blog pages
        $this->addPageRoute('/blog/post/{slug}','Blog','showBlogPost','blog>blog.post',null,'blogPostPage',false);
        // Relation map 
        $this->addRelationMap('post','Posts');
        // Create db tables
        $this->createDbTable('PostsSchema');     
    }   

    /**
     *  UnInstall extension
     *
     * @return void
     */
    public function unInstall()
    {
    }
}
