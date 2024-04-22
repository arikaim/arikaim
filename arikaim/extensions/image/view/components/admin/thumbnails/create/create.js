'use strict';

arikaim.component.onLoaded(function() {
    $('#thumbnail_size_dropdown').dropdown({
        onChange: function(value, text, item) { 
            $('#width').val($(item).attr('data-width'));
            $('#height').val($(item).attr('data-height'));
            $('#size').val($(item).attr('data-size'));           
        }
    });

    arikaim.ui.form.addRules("#create_thumbnail_form",{});

    arikaim.ui.form.onSubmit("#create_thumbnail_form",function() {  
        return thumbnailsControlPanel.create('#create_thumbnail_form',function(result) {
            arikaim.page.toastMessage(result.message);

            arikaim.page.loadContent({
                id: 'thumbnail_view_content',
                component: 'image::admin.thumbnails.item',
                append: true,
                params: { uuid: result.uuid }
            },function(result) {
                thumbnailsView.initRows();
            });    
        },function(error) {
            arikaim.page.toastMessage({
                message: error,
                class: error
            });
        });
    });
});