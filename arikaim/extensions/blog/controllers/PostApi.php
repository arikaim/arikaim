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
use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Controllers\Traits\Status;
use Arikaim\Core\Controllers\Traits\SoftDelete;
use Arikaim\Core\Controllers\Traits\MetaTags;

/**
 * Blog post control panel controler
*/
class PostApi extends ApiController
{
    use Status,
        MetaTags,
        SoftDelete;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('blog::admin.messages');
        $this->setModelClass('Posts');
        $this->setExtensionName('Blog');
    }

    /**
     * Empty trash
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function emptyTrash($request, $response, $data) 
    {         
        $data
            ->validate(true); 

        $result = Model::Posts('blog')
            ->softDeletedQuery()
            ->delete();

        if ($result === false) {
            $this->error('Error delete post');
            return false;
        }

        $this
            ->message('trash.empty');
    }

    /**
     * Add post
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function add($request, $response, $data) 
    {         
        $data
            ->addRule('text:min=2|required','title')
            ->validate(true); 

        $userId = $this->getUserId();
        $title = $data->get('title');      
        $post = Model::Posts('blog');

        if ($post->hasPost($title,$userId) == true) {
            $this->error('Blog post with this title exist.');
            return false;
        }
  
        $created = $post->create([
            'user_id'   => $userId,
            'content'   => $data['content'],
            'title'     => $title
        ]);

        if ($created === false) {
            $this->error('errors.post.add','Error create blog post.');
            return false;
        }
                                                         
        $this
            ->message('post.add')
            ->field('uuid',$created->uuid)         
            ->field('slug',$created->slug);           
    }

    /**
     * Update post
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function update($request, $response, $data) 
    {   
        $data
            ->addRule('text:min=2|required','title')
            ->validate(true);       
            
        $userId = $this->getUserId();
        $title = $data->get('title');   
        $uuid =  $data->get('uuid');

        

        $post = Model::Posts('blog')->findById($uuid);
        if ($post == null) {
            $this->error('errors.post.id','Not valid post id');
            return false;
        }
        
        // check access
        $this->requireUserOrControlPanel($userId);

        if ($post->hasPost($title,$userId) == true && $title != $post->title) {
            $this->error('errors.post.exist');
            return false;
        }
    
        $result = $post->update($data->toArray());              
    
        $this->setResponse(($result !== false),function() use($post) {                                                       
            $this
                ->message('post.update')
                ->field('uuid',$post->uuid)
                ->field('slug',$post->slug);           
        },'errors.post.update');
    }

    /**
     * Update image
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateImage($request, $response, $data) 
    {   
        $data           
            ->validate(true);       
            
        $imageId = $data->get('image_id');   
        $uuid =  $data->get('uuid');
        $post = Model::Posts('blog')->findById($uuid);
        if ($post == null) {
            $this->error('errors.post.id');
            return false;
        }
        
        $result = $post->update([
            'image_id' => $imageId
        ]);              
    
        $this->setResponse(($result !== false),function() use($post) {                                                       
            $this
                ->message('post.update')
                ->field('uuid',$post->uuid)
                ->field('slug',$post->slug);           
        },'errors.post.update');
    }

    /**
     * Update summary
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateSummaryController($request, $response, $data) 
    {   
        $data           
            ->validate(true);       

        $uuid = $data->get('uuid');
        $summary = $data->get('summary');   
        $userId = $this->getUserId();

        $post = Model::Posts('blog')->findById($uuid);
        if ($post == null) {
            $this->error('errors.post.id');
            return false;
        }
        
        // check access
        $this->requireUserOrControlPanel($userId);

        $result = $post->update([
            'summary' => $summary
        ]);              
    
        $this->setResponse(($result !== false),function() use($post) {                                                       
            $this
                ->message('post.update')
                ->field('uuid',$post->uuid);                    
        },'errors.post.update');
    }
}
