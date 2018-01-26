/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function Languages() {

    this.remove = function(uuid) {
        arikaim.delete('/admin/api/language/' + uuid,function(result) {          
            $('#view_button').click();
        },function (result) {
            // error remove language
        });
    };
    
    this.setDefault = function(uuid,onSuccess,onError) {
        arikaim.put('/admin/api/language/default/'+ uuid,null,onSuccess,onError);        
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
    }

    this.edit = function(uuid) {
        $('.languages-tab').removeClass('active');
        $('#edit_button').addClass('active');
        arikaim.page.loadContent({
            id: 'features_content',
            component: 'system:admin.languages.language.edit',
            params: {uuid: uuid}
        });  
    }

    this.moveAfter = function(uuid,after_uuid,onSuccess,onError) {
        arikaim.put('/admin/api/language/move/'+ uuid + '/' + after_uuid,null,onSuccess,onError);      
    };
}

var languages = new Languages();

arikaim.onPageReady(function() {
    $('.languages-tab').off();
    $('#languages_tab .item').tab();

    $('.languages-tab').on('click',function() {     
        $('.languages-tab').removeClass('active');
        $(this).addClass('active');
        var component_name = $(this).attr('component');
        arikaim.page.loadContent({
            id: 'features_content',
            component: component_name
        });     
    });
});