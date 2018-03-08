/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function Settings() {

    /**
     * Save setting variable
     * @param {string} name - name of setting variable
     * @param {*} value - variable value
     * @param {function} onSuccess - called after settings variable is saved.
     */
    this.save = function(name,value,onSuccess,onError) {
        var params = { 
            key: name,
            value: value 
        };
        arikaim.put('/admin/api/options/',params,onSuccess,onError);
    };

    this.saveAll = function(form_id,onSuccess,onError) {
        arikaim.post('/admin/api/options/',form_id,onSuccess,onError);
    };

    this.init = function() {
        $('#system_settings_tab').off();
        $('#system_settings_tab .item').tab();
    
        arikaim.page.loadContent({
            id: 'system_settings_content',
            component: 'system:admin.system.settings.user'
        });
    
        $('#system_settings_tab .item').on('click',function() {
            $('#system_settings_tab .item').removeClass('active');
            $(this).addClass('active');
            var component_name = $(this).attr('component');
            arikaim.page.loadContent({
                id: 'system_settings_content',
                component: component_name
            }); 
        });
    }

    /**
     * Get setting variable
     * @param {setting} name  - variable name
     * @param {function} onSuccess - called after reuest is done 
     */
    this.get = function(name,onSuccess) {
        arikaim.get('/admin/api/options/' + name,onSuccess);
    }
}

var settings = new Settings();

arikaim.page.onReady(function() {
    settings.init();
});