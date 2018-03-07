/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */


arikaim.page.onReady(function() {    
    
    $('#languages_list').sortable({        
        opacity: 0.8,
        cursor: "move",
        distance: 10,       
        update: function(event, ui) {           
            var prev_index = ui.item.index() - 1;       
            var uuid = ui.item.attr('id');
            var items = $("#languages_list").sortable("toArray");          
            var prev_item_uuid = items[prev_index];
            if (prev_index < 0) {
                prev_item_uuid = uuid;
            } 
            languages.move(uuid,prev_item_uuid,function(result) {
                languages.loadMenu();
            });         
        }
    });

    $('.language-button').off();
    $('.status-button.toggle').state({
        text: {
            active: "Disable",
            inactive: "Enable"  
        }
    });
      
    $('.remove-button').on('click',function() {
        var language = $(this).attr('language-title');
        var uuid = $(this).attr('uuid');
        confirmDialog.show({
                title: 'Remove',  
                description: 'Confirm remove ' + language + ' language',
                uuid: uuid
            },
            function(params) {
                languages.delete(params.uuid,function(result) {
                    $('#view_button').click();
                    languages.loadMenu();
                });
            }
        );        
    });
        
    $('.edit-button').on('click',function() {
        var uuid = $(this).attr('uuid');
        languages.edit(uuid);          
    });

    $('.change-status-button').on('click',function() {             
        var uuid = $(this).attr('uuid');
        languages.setStatus(uuid,'toggle',function(result) {
            if (result.status == 1) {
                $('#' + uuid).addClass('green');
            } else {
                $('#' + uuid).removeClass('green');
            }
            languages.loadMenu();
        });  
    });
    
    $('.set-default-button').on('click',function() {
        var uuid = $(this).attr('uuid');
        $('.default-language').hide();
        languages.setDefault(uuid,function(result) {           
            $('#'+ uuid).find('.default-language').removeClass('hidden').show();    
            $('#view_button').click();  
        });          
    });
    
    languages.init();
});
