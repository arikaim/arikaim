<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Image;

use Arikaim\Core\Extension\Extension;
use Arikaim\Extensions\Image\Classes\ImageLibrary;

/**
 * Image extension
*/
class Image extends Extension
{
    /**
     * Install extension routes, events, jobs
     *
     * @return void
    */
    public function install()
    {
        // Control Panel
        $this->addApiRoute('POST','/api/admin/image/upload','ImageControlPanel','upload','session');    
        $this->addApiRoute('POST','/api/admin/image/import','ImageControlPanel','import','session');              
        $this->addApiRoute('DELETE','/api/admin/image/delete/{uuid}','ImageControlPanel','delete','session'); 
        $this->addApiRoute('GET','/api/admin/image/list/{data_field}/[{query}]','ImageControlPanel','getList','session');             
        $this->addApiRoute('PUT','/api/admin/image/status','ImageControlPanel','setStatus','session'); 
        $this->addApiRoute('PUT','/api/admin/image/update','ImageControlPanel','update','session'); 
        // thumbnails
        $this->addApiRoute('POST','/api/admin/image/thumbnail/create','ThumbnailsControlPanel','create','session');              
        $this->addApiRoute('DELETE','/api/admin/image/thumbnail/{uuid}','ThumbnailsControlPanel','delete','session'); 
        // collections  
        $this->addApiRoute('POST','/api/admin/image/collections/create','CollectionsControlPanel','create','session');   
        $this->addApiRoute('PUT','/api/admin/image/collections/update','CollectionsControlPanel','update','session');  
        $this->addApiRoute('DELETE','/api/admin/image/collections/{uuid}','CollectionsControlPanel','delete','session');   
        // api 
        $this->addApiRoute('POST','/api/image/upload','ImageApi','upload','session');  
        $this->addApiRoute('POST','/api/image/import','ImageApi','import','session');          
        $this->addApiRoute('PUT','/api/image/status','ImageApi','setStatus',['session','token']);    
        $this->addApiRoute('GET','/api/image/view/{uuid}[/{file_name}]','ImageApi','view',null);    
        $this->addApiRoute('GET','/api/image/view/thumbnail/{slug}','ImageApi','viewThumbnail',null);
        $this->addApiRoute('GET','/api/image/svg/view/{name}[/{width}[/{height}]]','ImageApi','viewSvg',null);
        $this->addApiRoute('GET,PUT','/api/image/download/{uuid}','ImageApi','downloadImage',null);
        $this->addApiRoute('DELETE','/api/image/delete/{uuid}','ImageApi','delete','session');  
        // image relations
        $this->addApiRoute('PUT','/api/image/relations/main','ImageApi','setMainImage','session');  
        // qrcode (rquires qrcode module)
        $this->addApiRoute('POST','/api/admin/image/qrcode/generate','ImageControlPanel','generateQrCode','session');  

        // create db tables
        $this->createDbTable('Image');   
        $this->createDbTable('ImageRelations');                    
        $this->createDbTable('ImageThumbnails');   
        $this->createDbTable('ImageCollections');   
        $this->createDbTable('ImageCollectionItems');   
        // Console Commands
        $this->registerConsoleCommand('DeleteImages');          
        // Relation map 
        $this->addRelationMap('image','Image');
        // protected storage folder
        $this->createStorageFolder(ImageLibrary::IMAGES_PATH,false);
        // public storage folder
        $this->createStorageFolder(ImageLibrary::IMAGES_PATH,true);
        $this->createStorageFolder(ImageLibrary::IMAGES_PATH . 'thumbnails',true);
        // Services
        $this->registerService('ImageService');
        // Events
        $this->registerEvent('image.upload','Trigger after image is uplaoded.');
        $this->registerEvent('image.import','Trigger after image is imported from remote url.');
    }       

    /**
     * Post install actions
     *
     * @return void
     */
    public function postInstall()
    {
    }

    /**
     * Uninstall extension
     *
     * @return void
     */
    public function unInstall()
    {         
    }
}
