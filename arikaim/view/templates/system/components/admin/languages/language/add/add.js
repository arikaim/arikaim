/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.page.onReady(function() {

    var languages_list;

     arikaim.component.load('system:config.languages',function(result) {
        languages_list =  arikaim.component.get('system:config.languages');
    });
   
    arikaim.page.loadContent({
        id: 'form_content',
        component: 'system:admin.languages.language.form',
        loader: false
    });

    $('#languages_list').dropdown({
        onChange: function(code) {
            var language = languages_list.getProperty(code);
            $('#code').val(language.code);
            $('#code_3').val(language.code_3);
            $('#title').val(language.title);
            $('#native_title').val(language.native_title);
            if (isEmpty(language.country_code) == false) {
                $('#country_code').dropdown('set selected',language.country_code.toLowerCase());
            }
        }
    });  
});