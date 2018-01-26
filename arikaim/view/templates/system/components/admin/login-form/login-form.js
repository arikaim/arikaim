/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.onPageReady(function () {

  arikaim.form.addRules("login_form",{
    inline: false,
    fields: {
      password: {
        identifier: "password",
        rules: [{
          type: "empty"   
        }]
      },
      user_name: {
        identifier: "user_name",      
        rules: [{
          type: "empty"                
        }]
      }
    }
  });

  arikaim.form.onSubmit('login_form',function() {
    controlPanelUser.adminLogin();
  });

});