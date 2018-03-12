/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function Languages() {

    var tab_item = '.languages-tab';

    this.delete = function(uuid,onSuccess,onError) {
        arikaim.delete('/admin/api/language/' + uuid,onSuccess,onError); 
    };
    
    this.setDefault = function(uuid,onSuccess,onError) {
        arikaim.put('/admin/api/language/default/'+ uuid,null,onSuccess,onError);        
    };

    this.loadMenu = function(menu_element) {
        if (isEmpty(menu_element) == true) {
            var menu_element = "language-menu";
        }
        arikaim.page.loadContent({
            id : menu_element,
            component : 'system:language-menu'
        });
    };

    /**
     * 
     * @param string uuid 
     * @param int status  0 - disabled, 1 - active, 2 - default
     */
    this.setStatus = function(uuid,status,onSuccess,onError) {     
        var status_text = isEmpty(status) ? 'toggle' : status;         
        arikaim.put('/admin/api/language/status/'+ uuid + '/' + status_text,null,onSuccess,onError);      
    };

    this.load = function(uuid) {
        arikaim.page.loadContent({
            id : 'form_content',
            component : 'system:admin.languages.language.form',
            params: { 'uuid': uuid },
            loader : false
        });
    };

    this.edit = function(uuid) {
        $(tab_item).removeClass('active');
        $('#edit_button').addClass('active');
        arikaim.page.loadContent({
            id: 'features_content',
            component: 'system:admin.languages.language.edit',
            params: { uuid: uuid }
        });  
    };

    this.move = function(uuid,after_uuid,onSuccess,onError) {
        arikaim.put('/admin/api/language/move/'+ uuid + '/' + after_uuid,null,onSuccess,onError);      
    };

    this.init = function() {
        $(tab_item).off();
        $(tab_item).on('click',function() {
            $(tab_item).removeClass('active');
            $(this).addClass('active');
            var component_name = $(this).attr('component');
            arikaim.page.loadContent({
                id: 'features_content',
                component: component_name
            });     
        });
    };
}

var languages = new Languages();

arikaim.page.onReady(function() {
    languages.init();
});