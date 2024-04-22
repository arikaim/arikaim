/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function LanguagesView() {
    var self = this;

    this.init = function() {
        this.loadMessages('system:admin.languages');

        var startIndex = null;
        var endIndex = null;
        var items = [];

        $('#languages_list').sortable({        
            opacity: 0.8,
            cursor: "move",
            distance: 10,   
            create: function(event, ui) {
                items = $("#languages_list").sortable("toArray");    
            },
            start: function(event, ui) { 
                startIndex = ui.item.index();  
            },   
            stop: function(event, ui) {
                endIndex = ui.item.index();
                var uuid = items[startIndex];
                var targetUuid = items[endIndex];

                if (uuid != targetUuid) {
                    position.shift("Language",uuid,targetUuid,function(result) {
                       
                    });     
                }
            }
        });

        languagesView.initRows();
    };

    this.initRows = function() {
        arikaim.ui.button('.remove-button', function(element) {
            var language = $(element).attr('language-title');
            var uuid = $(element).attr('uuid');
            var message = arikaim.ui.template.render(self.getMessage('description'),{ title: language });
            
            modal.confirmDelete({
                    title: self.getMessage('title'),  
                    description: message,
                    uuid: uuid
            }).done(function(params) {
                return languages.delete(params.uuid).done(function(result) {
                    $('#view_button').click();
                    languages.loadMenu();
                });
            });        
        });
        
        arikaim.ui.button('.edit-button',function(element) {
            var uuid = $(element).attr('uuid');
            arikaim.ui.setActiveTab('#edit_button');
            return arikaim.page.loadContent({
                id: 'tab_content',
                component: 'system:admin.languages.language.edit',
                params: { uuid: uuid }
            });           
        });
    
        arikaim.ui.button('.change-status-button',function(element) {             
            var uuid = $(element).attr('uuid');
            return languages.setStatus(uuid,'toggle').done(function(result) {
                if (result.status == 1) {
                    $('#' + uuid).addClass('green');
                } else {
                    $('#' + uuid).removeClass('green');
                }
                languages.loadMenu();
            }).fail(function(error) {
                console.log(error);
            });  
        });
        
        arikaim.ui.button('.set-default-button',function(element) {
            var uuid = $(element).attr('uuid');            
            $('.default-language').hide();

            return languages.setDefault(uuid).done(function(result) {           
                $('#'+ uuid).find('.default-language').removeClass('hidden').show();    
                $('#view_button').click();  
            }).fail(function(error) {
                console.log(error);
            });        
        });
    }
}

var languagesView = createObject(LanguagesView,ControlPanelView);

arikaim.component.onLoaded(function() {    
    languagesView.init();
   
});
