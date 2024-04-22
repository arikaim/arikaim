<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Document;

/**
 * Linked document items table trait
*/
trait LinkedDocumentItems  
{ 
   
    public function linkDocument($id)
    {
        $this->linked_document_id = $id;
        $this->update([
            'linked_document_id' => $id
        ]);

        $this->updateLinkedDocumentItems();
    }

    public function updateLinkedDocumentItems()
    {
        if (empty($this->linked_document_id) == true) {
            return false;
        }

    } 
}
