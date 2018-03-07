/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.page.onReady(function () {
    arikaim.form.addRules("#config_form",{
        inline: false,
        fields: {
            database: {
                rules: [{ type: "minLength[3]" }]
            },
            username: {
                rules: [{ type: "minLength[3]" }]
            },
            password: {
                rules: [{ type: "minLength[3]" }]
            }
        }
    });
});  