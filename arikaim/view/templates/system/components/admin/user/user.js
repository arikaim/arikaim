/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function ControlPanelUser() {
    
    this.isLoged = function() {
        return arikaim.isValidToken();
    };

    this.changeDetails = function(form_id,onDone,onError) {
        var form_id = getDefaultValue(form_id,'user_settings_form');
        arikaim.post('/admin/api/user/',form_id,function(result) {
            // saved 
            callFunction(onDone,result);            
        },function(errors) {
            // error
            callFunction(onError,errors);                           
        }); 
    }

    this.adminLogin = function(form_id) {
        var form_id = getDefaultValue(form_id,'login_form');
       
        arikaim.post('/admin/api/user/login/',form_id,function(result) {
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
        },"session");
    };

    this.adminLogout = function() {      
        arikaim.get('/admin/api/user/logout/',function(result) {         
            arikaim.setToken("");
            arikaim.reloadPage();           
        },function(errors) {
            return false;
        },
        "session");     
    };
};

var controlPanelUser = new ControlPanelUser();