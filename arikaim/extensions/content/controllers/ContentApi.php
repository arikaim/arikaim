<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Content\Controllers;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Db\Model;

use Arikaim\Core\Controllers\Traits\Status;

/**
 * Content api controller
*/
class ContentApi extends ApiController
{
    use Status;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('content>content.messages');
        $this->setModelClass('Content');
        $this->setExtensionName('content');
    }

    /**
     * delete content item
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return mixed
    */
    public function delete($request, $response, $data) 
    { 
        $data
            ->addRule('text:min=2','uuid')                     
            ->validate(true); 

        $item = Model::Content('content')->findById($data['uuid']);   
        if ($item  == null) {
            $this->error('Content item id not vlaid');
            return false;
        }

        // check access
        $this->requireUserOrControlPanel($item->user_id);

        $item->delete();

        $this
            ->message('delete')
            ->field('uuid',$data['uuid']);       
    }

    /**
     * Add content item
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return mixed
    */
    public function add($request, $response, $data) 
    { 
        $data
            ->addRule('text:min=2','key')         
            ->addRule('text:min=2','content_type')             
            ->validate(true); 
            
        $key = $data->get('key');  
        $contentType = \trim($data->get('content_type')); 
        $title = $data->get('title');   
        $public = $data->get('public',true);   
        $userId = ($public == true) ? null : $this->getUserId(); 
        
        $content = Model::Content('content');   
        if ($content->hasContentItem($key,$userId) == true) {
            $this->error('Content item key are used');
            return false;
        }

        // check content type
        if ($this->get('content')->hasContentType($contentType) == false) {
            $this->error('Not valid content type');
            return false;
        }

        $contentProvider = $this->get('content')->type($contentType);
        $contentItem = $contentProvider->createItem([
            'user_id' => $userId
        ]);

        if ($contentItem == null) {
            $this->error('Error create content item');
            return false;
        }

        $item = $content->create([
            'key'          => $key,
            'user_id'      => $userId,
            'title'        => $title,
            'content_type' => $contentType,
            'content_id'   => $contentItem[0]
        ]);

        if ($item == null) {
            $this->error('Error create content item');
            return false;
        }

        $this
            ->message('add')
            ->field('key',$item->key)              
            ->field('uuid',$item->uuid);         
    }

    /**
     * Save content item
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return mixed
    */
    public function update($request, $response, $data) 
    {       
        $data
            ->addRule('text:min=2','key')         
            ->validate(true);     

        $key = $data->get('key');  
        $fields = $data->get('fields',[]);
        $public = $data->get('public',true);   
        $userId = ($public == true) ? null : $this->getUserId(); 

        $item = Model::Content('content')->findByKey($key,$userId);    
        if ($item == null) {
            $this->error('Not valid content key');
            return false;
        }
      
        $provider = $this->get('content')->getDefaultProvider($item->content_type);
        if ($provider == null) {
            $this->error('Not valid content type');
            return false;
        }
        
        $result = $provider->updateOrCreate($item->content_id,$fields);
        if ($result === false) {
            $this->error('Error save content item data');
            return false;
        }

        $this
            ->message('update')
            ->field('key',$item->key)              
            ->field('item_uuid',$item->uuid);             
    }
}
