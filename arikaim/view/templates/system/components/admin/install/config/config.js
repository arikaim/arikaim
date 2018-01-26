
arikaim.onPageReady(function () {

    arikaim.form.addRules("config_form",{
      inline: false,
      fields: {
        database: {
          identifier: "database",
          rules: [{
            type: "empty"   
          }]
        },
        username: {
          identifier: "username",      
          rules: [{
            type: "empty"                
          }]
        },
        password: {
          identifier: "password",      
          rules: [{
            type: "empty"                
          }]
        }
      }
    });
  
});  