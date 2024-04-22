/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ImagePreviewModal() { 
  
    this.loadCode = function() {
        arikaim.component.loadContent('semantic~image.modal',function(result) {
            $('body').append(result.html);           
        });       
    };

    this.show = function(options) {
        var modalId = getValue('id',options,'image_preview_modal');
        var title = getValue('title',options,null);
        var description = getValue('description',options,null);
        var images = getValue('images',options,[]);
        var icon = getValue('icon',options,null);
        var hideIcon = getValue('hideIcon',options,false);
        var hideTitle = getValue('hideTitle',options,true);

        images.forEach(function(item) {
            console.log(item);
            $('#image_preview').attr('data-src',item);
        });
        if (isEmpty(title) == false) {
            $('#' + modalId + ' .image-modal-title').html(title);
        }
        if (isEmpty(description) == false) {
            $('#' + modalId + ' .image-modal-description').html(description);
        }
        if (isEmpty(icon) == false) {
            $('#' + modalId + ' .image-modal-icon').attr('class','icon modal-icon ' + icon);           
        }
        if (hideIcon == true) {
            $('#' + modalId + ' .image-modal-icon').hide();
        }
        if (hideTitle == true) {
            $('#image_modal_header').hide();
        }
        // show modal
        $('#' + modalId).modal({}).modal('show');
        // load images
        arikaim.ui.initImageLoader();
    };
}

var imagePreviewModal = new ImagePreviewModal();

arikaim.component.onLoaded(function(component) {
    imagePreviewModal.loadCode();
});
