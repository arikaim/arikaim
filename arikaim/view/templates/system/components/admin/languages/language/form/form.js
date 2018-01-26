/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.onPageReady(function() {
  $('#country_code').dropdown();
  var component = arikaim.getComponent('system:admin.languages.language.form');

  arikaim.form.addRules("language_form",{
    inline: false,
    fields: {
      code: {
        identifier: "code", 
        rules: [{
            type:'exactLength[2]'
        }]
      },
      code_3: {
        identifier: "code_3",
        rules: [{
          type: 'exactLength[3]'         
        }]
      },
      title: {
        identifier: "title",      
        rules: [{
          type: "empty",
          prompt: component.getProperty('title.prompt')                
        }]
      },
      country: {
        identifier: "country_code",      
        rules: [{
          type: "empty",
          prompt: component.getProperty('country.prompt')                    
        }]
      }
    }
  });
      
  arikaim.form.onSubmit('language_form',function() {
    arikaim.post('/admin/api/language/','language_form',function(result) {
      $('#view_button').click();
    },function(errors) {
      arikaim.form.showErrors(errors,'.form-errors',component);
    });  
  });
   
});