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
     * @param {function} onDone - called after settings variable is saved.
     */
    this.save = function(name,value,onDone,onError) {
        var params = { 
            key: name,
            value: value 
        };
        arikaim.put('/admin/api/options/',params,onDone,onError);
    };

    this.saveAll = function(form_id,onDone,onError) {
        arikaim.post('/admin/api/options/',form_id,onDone,onError);
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
     * @param {function} onDone - called after reuest is done 
     */
    this.get = function(name,onDone) {
        arikaim.get('/admin/api/options/' + name,onDone);
    }
}

var settings = new Settings();

arikaim.onPageReady(function() {
    settings.init();
});