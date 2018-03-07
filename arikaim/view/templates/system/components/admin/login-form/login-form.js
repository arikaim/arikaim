/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.page.onReady(function () {

    arikaim.form.addRules("#login_form",{
        inline: false,
        fields: {
            password: {
                rules: [{ type: "empty" }]
            },
            user_name: {
                rules: [{ type: "empty" }]
            }
        }
    });

    arikaim.form.onSubmit('#login_form',function() {
        $('.login-button').addClass('loading');
        controlPanelUser.adminLogin(function(result) {
            $('.login-button').removeClass('loading');
        },function(errors) {
            $('.login-button').removeClass('loading');
        });
    });
});