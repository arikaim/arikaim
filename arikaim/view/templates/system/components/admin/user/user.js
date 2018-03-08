/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function ControlPanelUser() {
    
    var form_element = '#login_form';

    this.isLoged = function() {
        return arikaim.isValidToken();
    };

    this.changeDetails = function(form_element,onSuccess,onError) {
        var form_element = getDefaultValue(form_element,'#user_settings_form');
        arikaim.post('/admin/api/user/',form_element,function(result) {
            // saved 
            callFunction(onSuccess,result);            
        },function(errors) {
            // error
            callFunction(onError,errors);                           
        }); 
    }

    this.adminLogin = function(onSuccess,onError) {
        arikaim.post('/admin/api/user/login/',form_element,function(result) {
            callFunction(onSuccess,result);  
            arikaim.setToken(result);
            arikaim.page.loadContent({
                component: 'system:admin.layout',
                id: 'content',
                transition: 'scale'
            });
            arikaim.page.loadContent({
                component: 'system:admin.user-menu',
                id: 'user_menu',
                transition: 'scale'
            });               
        },function (errors) {
            arikaim.form.showErrors(errors,'.form-errors');     
            callFunction(onError,errors);  
        },"session");
    };

    this.adminLogout = function() {      
        arikaim.get('/admin/api/user/logout/',function(result) {         
            arikaim.clearToken();
            arikaim.page.reload();           
        },function(errors) {
            return false;
        },
        "session");     
    };
};

var controlPanelUser = new ControlPanelUser();