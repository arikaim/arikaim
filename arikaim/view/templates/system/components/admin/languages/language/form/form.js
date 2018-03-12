/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.page.onReady(function() {
    $('#country_code').dropdown();
    $('#language_dropdown').dropdown();
    var component = arikaim.getComponent('system:admin.languages.language.form');

    arikaim.form.addRules("#language_form",{
        inline: false,
        fields: {
            code: {
                rules: [{ type:'exactLength[2]' }]
            },
            code_3: {
                rules: [{ type: 'exactLength[3]' }]
            },
            title: {
                rules: [{
                    type: "empty",
                    prompt: component.getProperty('title.prompt')                
                }]
            },
            country_code: {
                rules: [{
                    type: "empty",
                    prompt: component.getProperty('country.prompt')                    
                }]
            }
        }
    });
      
    arikaim.form.onSubmit('#language_form',function() {
        $('#save_language_button').addClass('loading');
        arikaim.post('/admin/api/language/','#language_form',function(result) {
            $('#save_language_button').removeClass('loading');
            $('#view_button').click();
            languages.loadMenu();
        },function(errors) {
            $('#save_language_button').removeClass('loading');
            arikaim.form.showErrors(errors,'.form-errors');
            arikaim.form.addFieldErrors('#language_form',errors);
        });  
    });
});